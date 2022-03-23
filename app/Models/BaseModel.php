<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Services\CustomLogService;
use Lang;
use App\Helpers\Helper;

class BaseModel extends Model
{
    const ENABLE = true;
    const DISABLE = false;

    const TIME_FORMAT = 'Y/m/d H:i:s';
    const DATE_FORMAT = 'Y/m/d';
    const DATE_MULTI_FORMAT = 'Y/m/d, Y-m-d';
    const MONTH_FORMAT = 'Y/m';
    const INT_MAXLENGTH = '999999999999';
    const TEXT_MAXLENGTH = '300';
    const PHONE_NUMBER = 'regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:20';
    const INT_FLOAT_FORMAT = 'regex:/^\d{0,2}(\.\d{0,2}){0,1}$/';
    const KANA_STRING = 'regex:/^([ァ-ヶー・ヽヾ「」]+)$/u';
    const ERROR_VERSION_CODE = 4000;
    const LENGTH_NAME_DOCUMENT = 80;
    const ERROR_OTHER = 4001;
    const SORT_TYPE = [
        'asc',
        'desc'
    ];

    // Search constant parameter defined
    // query type
    const LIKE_QUERY = 0;
    const MATCH_QUERY = 1;

    // Search condition
    const LEFT_JOIN_CONDITION = 0;
    const DEFAULT_CONDITION = 1;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table;

    /**
     * The field array of table.
     *
     * @var array
     */
    protected $fillable;

    /**
     * Default order array.
     *
     * @var array
     */
    protected $order_field;

    /**
     * Default search array.
     *
     * @var array
     */
    protected $search_field;

    /**
     * The code field.
     *
     * @var array
     */
    protected $code_field;

    /**
     * Add customize validate for table
     * 
     * @param string $id  
     * @return array
     */
    public function CustomValidate($request, $default_rule)
    {
        return $default_rule;
    }

    /**
     * Set validate default of modal table
     * 
     * @param string $id  
     * @return array
     */
    public function fieldSetValidate($id = null)
    {
        $result = [];
        return $result;
    }

    /** 
     * Get all field of table
     * 
     * @return array
     */
    public function get_field_table()
    {
        return $this->fillable;
    }

    /** 
     * Get search array
     * 
     * @return array
     */
    public function get_search_array()
    {
        return $this->search_field;
    }

    /** 
     * Get order array
     * 
     * @return array
     */
    public function get_order_array()
    {
        return $this->order_field;
    }

    /** 
     * Get order array for search
     * 
     * @return array
     */
    public function get_order_search($input)
    {
        return $this->order_field;
    }

    /** 
     * Get field code
     * 
     * @return array
     */
    public function get_code_field()
    {
        return $this->code_field;
    }

    /** 
     * Get name of table
     * 
     * @return array
     */
    public function get_table_name()
    {
        return $this->table;
    }

    /**
     * Escape special characters for a LIKE query.
     *
     * @param string $value
     * @param string $char
     *
     * @return string
     */
    private static function escape_like(string $value, string $char = '\\'): string
    {
        return str_replace(
            [$char, '%', '_'],
            [$char.$char, $char.'%', $char.'_'],
            $value
        );
    }

    /**
     * Prevent update not change some field
     * 
     * @param string $id  
     * @return array
     */
    protected static function prevent_update($input)
    {
        $do_not_update_field = [ 'created_at', 'updated_at', 'is_deleted', 'created_by', 'updated_by'];

        foreach($do_not_update_field as $field)
        {
            if(isset($input[$field])){
                unset($input[$field]);
            }
        }
        return $input;
    }

    /**
     *  Get object by id
     * 
     * @param $id
     * @return object
     */
    protected static function getById($id)
    {
        $query = self::where('id',$id)->where('is_deleted', false)->first();

        return empty($query) ? false : $query;
    }

    /**
     *  Update relation data with input array
     * 
     * @param $id
     * @return object
     */
    protected static function updateArrayRelation($input, $update_key, $table_log_name, $condition = NULL, $insert_condition = NULL)
    {
        $check_query = self::where('is_deleted', false);

        if(!empty($condition))
        {
            foreach($condition as $key => $value)
            {
                $check_query = $check_query->where($key, $value);
            }
        }

        // check data
        if((clone $check_query)->count() != (clone $check_query)
            ->whereIn($update_key, $input)->count() || 
            (clone $check_query)->count() != count($input))
        {
            $created_obj_list = [];
            foreach ($input as $element) {
                    
                if((clone $check_query)
                    ->where($update_key, $element)->count() == 0)
                {
                    $insert_data = [];
                    if(!empty($condition))
                        foreach($condition as $key => $value)
                        {
                            $insert_data[$key] = $value;
                        }

                    if(!empty($insert_condition))
                        foreach($insert_condition as $key => $value)
                        {
                            $insert_data[$key] = $value;
                        }
                    $insert_data[$update_key] = $element;
                    self::default_store($insert_data, $table_log_name, "");
                }
    
                array_push($created_obj_list, $element);
            }

            // find and remove not in array record
            (clone $check_query)->whereNotIn($update_key, $created_obj_list)
                ->update(['is_deleted' => true]);
        }

    }

    /**
     *  Get object by code
     * 
     * @param $code_field, $code
     * @return mixed
     */
    protected static function getByCode($code_field, $code)
    {
        $query = null;
        if(isset($code_field))
        {
            $query = self::where($code_field ,$code)->where('is_deleted', false)->first();
        }

        return empty($query) ? false : $query;
    }

    /**
     *  Get default list index
     * 
     * @param $pagination, $order_aray
     * @return mixed
     */
    protected static function default_list($pagination, $order_aray)
    {
        $index_query = self::where('is_deleted', false);

        if(isset($order_aray) && count($order_aray) > 0)
        {
            foreach($order_aray as $array){
                $index_query = $index_query->orderBy($array[0], $array[1]);
            }
        }

        if($pagination != null)
            return $index_query->paginate($pagination);
        
        return $index_query;
    }

    /**
     *  Get default search by params
     * 
     * @param $input, $pagination, $search_array, $order_aray
     * @return mixed
     */
    protected static function default_search($input, $pagination, $search_array, $order_aray)
    {
        // igrore sort query params
        unset($input['order_by']);
        unset($input['order_sort']);

        if(count($input) > 0)
        {
            $query = self::where(function ($query) use ($input, $search_array) {
                foreach ($search_array as $field) {
                    $search_field = $field[0];

                    if (!isset($input[$field[0]]) && $field[1] != 6) {
                        continue;
                    }

                    $search_values = isset($input[$search_field]) ? $input[$search_field] : "";
                    
                    if (is_string($search_values)) {
                        $search_values = self::escape_like($search_values);
                    }
                    
                    if(1 <= $field[1] && $field[1] <= 4){
                        if(count($field) >= 3)
                            $search_field = $field[2];
                    }

                    switch($field[1]) {
                        case 1:   // match
                            $query = $query->where($search_field, $search_values);
                            break;
                        case 2:   // like
                            $query = $query->where($search_field, 'LIKE', '%' . $search_values . '%');
                            break;
                        case 3:   // time greater than (gte)
                            if(!Helper::validateDate($search_values, self::DATE_FORMAT))
                                $query = $query->where('id', '-1');
                            else {
                                $compare_sign = ">=";
                                if(count($field) >= 4)
                                    $compare_sign = $field[3];
                                $query = $query->where($search_field, $compare_sign, date($search_values));
                            }
                            break;
                        case 4:   // time less than (lte)
                            if(!Helper::validateDate($search_values, self::DATE_FORMAT))
                                $query = $query->where('id', '-1');
                            else {
                                $compare_sign = "<=";
                                if(count($field) >= 4)
                                    $compare_sign = $field[3];
                                $query = $query->where($search_field, $compare_sign, date($search_values));
                            }
                            break;
                        default:
                            break;
                    }
                }
            });
            $search_query = $query->where('is_deleted', false);
        }
        else
        {
            $search_query =  self::where('is_deleted', false);
        }

        if(isset($order_aray) && count($order_aray) > 0)
        {
            foreach($order_aray as $array){
                $search_query = $search_query->orderBy($array[0], $array[1]);
            }
        }
        
        if($pagination != null)
            return $search_query->paginate($pagination);

        return $search_query;
    }

    /**
     *  Check request data before create object 
     * 
     * @param $input
     * @return mixed
     */
    protected static function check_store($input)
    {
        return [
            'status' => true,
            'message' => ''   
        ];
    }

    /**
     *  Change request data to create new object
     * 
     * @param $input
     * @return mixed
     */
    protected static function before_store($request, $input)
    {
        return $input;
    }

    /**
     *  Default create new object
     * 
     * @param $input, $table , $code
     * @return mixed
     */
    protected static function default_store($input, $table , $code)
    {
        $object = self::create($input)->toArray();
        
        $log_message = "[Model: ".$table . "]  ";

        if(!empty($code) && isset($object[$code])) {    
            $log_message .= "Insert data id: ".
                    $object['id']."--- code: ".$object[$code];
        }
        else {
            $log_message .= "Insert data id: ".$object['id'];
        }

        CustomLogService::info(__FILE__, __LINE__,__CLASS__, $log_message);

        $field = self::find($object['id']);
        return $field;
    }

    /**
     *  Change data after create new object
     * 
     * @param $id, $input
     * @return mixed
     */
    protected static function after_store($object, $input, $request)
    {
        return;
    }

    /**
     *  Check request data before update object
     * 
     * @param $input
     * @return mixed
     */
    protected static function check_update($input, $object)
    {
        if(
            isset($input['udp_ver'])
            && isset($object['udp_ver'])
            && $input['udp_ver'] != $object['udp_ver']
        ) {
            return [
                'status' => false,
                'code' => self::ERROR_VERSION_CODE,
                'message' => Lang::get('api.error_message.udp_error')
            ];
        }

        return [
            'status' => true,
            'message' => ''   
        ];
    }

    /**
     *  Change request data to update object
     * 
     * @param $request, $input, $object
     * @return mixed
     */
    protected static function before_update($request, $input, $object)
    {
        return $input;
    }

    /**
     *  Default update object
     * 
     * @param $object, $input, $list_field, $table , $code
     * @return mixed
     */
    protected static function default_update($object, $input, $table , $code)
    {
        $object->update($input);

        $log_message = "[Model: ".$table . "]  ";

        if(!empty($code) && isset($object[$code])) {    
            $log_message .= "Update data id: ".
                    $object['id']."--- code: ".$object[$code];
        }
        else {
            $log_message .= "Update data id: ".$object['id'];
        }

        CustomLogService::info(__FILE__, __LINE__,__CLASS__, $log_message);

        return $object;
    }

    /**
     *  Change data after update object
     * 
     * @param $object, $input
     * @return mixed
     */
    protected static function after_update($object, $input, $request)
    {
        return;
    }

    /**
     *  Check request data before delete object
     * 
     * @param $input
     * @return mixed
     */
    protected static function check_delete($input, $object)
    {
        if($input['udp_ver'] != $object['udp_ver'])
        {
            return [
                'status' => false,
                'code' => self::ERROR_VERSION_CODE,
                'message' => Lang::get('api.error_message.udp_error')
            ];
        }

        return [
            'status' => true,
            'message' => ''   
        ];
    }

    /**
     *  Default delete object
     * 
     * @param $object, $table , $code
     * @return mixed
     */
    protected static function default_delete($object, $table , $code)
    {
        $object['is_deleted'] = true;
        $object->save();

        $log_message = "[Model: ".$table . "]  ";

        if(!empty($code) && isset($object[$code])) {    
            $log_message .= "Delete data id: ".
                    $object['id']."--- code: ".$object[$code];
        }
        else {
            $log_message .= "Delete data id: ".$object['id'];
        }

        CustomLogService::info(__FILE__, __LINE__,__CLASS__, $log_message);

        return $object;
    }

    /**
     *  Change data after delete object
     * 
     * @param $object, $input
     * @return mixed
     */
    protected static function after_delete($object)
    {
        return;
    }

    /**
     *  Default export object
     * 
     * @param $object, $input
     * @return mixed
     */
    protected static function default_export($input, $search_array, $order_aray)
    {
        return self::default_search($input, null, $search_array, $order_aray)->get();
    }
}

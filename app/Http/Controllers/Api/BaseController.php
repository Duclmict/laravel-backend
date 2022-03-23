<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Http\Resources\Api\ApiStatus;
use App\Services\CustomLogService;
use App\Helpers\Helper;
use DB;
use Lang;
use Illuminate\Support\Facades\Response as Download;
use Illuminate\Support\Facades\Storage;

/**
 * Class BaseController
 * @package App\Http\Controllers\API
 */

/**
 * @SWG\Swagger(
 *   basePath="/",
 *   @SWG\Info(
 *     title="不動産管理システム API",
 *     version="1.0.0"
 *   )
 * )
 */
class BaseController extends Controller
{
    /** Pagination number
     *
     * @var int
     */
    protected $pagination = 10;

    /** This name of controller
     *
     * @var $name
     */
    protected $name;

    /** This modal
     *
     * @var $modal
     */
    protected $modal;

    /** This resource
     *
     * @var $resource
     */
    protected $resource;

    /** Collection resource for pagination
     *
     * @var string
     */
    protected $collection;

    /** Simple resource
     *
     * @var $resource
     */
    protected $resource_index;

    /** Collection of simple resource for pagination
     *
     * @var string
     */
    protected $collection_index;

    /** Export name
     *
     * @var string
     */
    protected $form_export;

    /** Export excel name
     *
     * @var string
     */
    protected $form_excel_export;

    /** Error message : No object after find
     * @var string
     */
    protected $no_object_error_msg;
    protected $login_error_msg;
    protected $create_token_error_msg;
    protected $mail_error_msg;
    protected $logout_success_msg;
    protected $logout_fail_msg;
    protected $process_success_msg;
    protected $update_version_msg;
    protected $deleted_user_msg;
    protected $mail_msg;
    /**
     * BaseController constructor.
     */
    public function __construct()
    {
        $this->no_object_error_msg      = Lang::get('api.error_message.no_object');
        $this->login_error_msg          = Lang::get('api.error_message.login_failed');
        $this->create_token_error_msg   = Lang::get('api.error_message.create_token_error');
        $this->mail_error_msg           = Lang::get('api.error_message.mail_error');
        $this->logout_success_msg       = Lang::get('api.error_message.logout_success');
        $this->logout_fail_msg          = Lang::get('api.error_message.logout_error');
        $this->process_success_msg      = Lang::get('api.error_message.process_success');
        $this->update_version_msg       = Lang::get('api.error_message.udp_error');
        $this->deleted_user_msg         = Lang::get('api.error_message.deleted_user_login');
        $this->mail_msg                 = Lang::get('api.error_message.mail');
        $this->not_import_file          = Lang::get('api.error_message.not_import_file');
        $this->length_import_over_10mb  = Lang::get('api.error_message.length_import_over_10mb');
        $this->import_format_failed     = Lang::get('api.error_message.import_format_failed');
    }

    /** Send token
     *
     * @param $token
     * @param $success_code
     * @return array
     */
    protected function sendToken($token, $success_code)
    {
        return [
            'response' => [
                'data' => ['token' => $token],
                'status' => $success_code,
                'message' => $this->process_success_msg
            ]
        ];
    }

    /** Set header Token for api
     *
     * @param $token
     * @return array
     */
    protected function setHeaderToken($token)
    {
        $this->headers = [
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ];
    }

    /** Send success format
     *
     * @param $ret_data
     * @param $success_code
     * @return array
     */
    protected function sendResponse($ret_data, $success_code)
    {
        return [
            'response' => [
                'data' => $ret_data,
                'status' => $success_code,
                'message' => $this->process_success_msg
            ]
        ];
    }
    
    /** Send error format support
     *
     * @param $apiStatus
     * @param $custom_message
     * @return array
     */
    private function sendErrorResponse($apiStatus, $custom_message, $code = null) {
        
        if($code == null )
            return response()->json( [
                'response' => [
                    'status' => $apiStatus->getStatusCode(),
                    'message' => $custom_message ? $custom_message : $apiStatus->getStatusMsg()
                ]
            ], $apiStatus->getStatusCode());
        else
            return response()->json( [
                'response' => [
                    'status' => $apiStatus->getStatusCode(),
                    'message' => $custom_message ? $custom_message : $apiStatus->getStatusMsg(),
                    'code' => $code
                ]
            ], $apiStatus->getStatusCode());
    }

    /** Error not found send
     *
     * @param $message
     * @return array
     */
    protected function sendErrorNotFound($message = null, $code = null)
    {
        return $this->sendErrorResponse(new ApiStatus(404), $message, $code);
    }

    /** Error conflict send
     *
     * @param $message
     * @return array
     */
    protected function sendErrorConfilct($message = null, $code = null)
    {
        return $this->sendErrorResponse(new ApiStatus(409), $message, $code);
    }

    /** Error bad request send
     *
     * @param $message
     * @return array
     */
    protected function sendErrorBadRequest($message = null, $code = null)
    {
        return $this->sendErrorResponse(new ApiStatus(400), $message, $code);
    }
  
    /** Send un-authorized error format
     *
     * @param $message
     * @return array
     */
    protected function sendErrorUnAuthorized($message = null, $code = null)
    {
        return $this->sendErrorResponse(new ApiStatus(401), $message, $code);
    }
  
    /** Error internal server send
     *
     * @param $message
     * @return array
     */
    protected function sendErrorServerInternal($message = null, $code = null)
    {
        return $this->sendErrorResponse(new ApiStatus(500), $message, $code);
    }

    // BASE METHOD

    /** fallback json not found if api not matching route
     *
     * @return array
     */
    public function fallback()
    {
        return response()->json( [
            'response' => [
                'status' => [
                    'code' => 404,
                    'message' => 'Not found'
                ]
            ]
        ], 404);
    }

    /** Display the all resource in storage.
     *
     * @return array
     */
    public function index(Request $request)
    {
        CustomLogService::info(__FILE__, __LINE__,__CLASS__, 
            " start get all data ".$this->modal);

        if(isset($request['limit']) && $request['limit'] != null)
            $current_pagi = $request['limit'];
        else
            $current_pagi = $this->pagination;

        CustomLogService::info(__FILE__, __LINE__,__CLASS__, 
            "get all base data ".$this->modal);

        return $this->sendResponse(
            new $this->collection_index(
                $this->modal::default_list($current_pagi,
                (new $this->modal)->get_order_array())
            ), 200);
    }

    /** Get all resource in storage.
     *
     * @return array
     */
    public function list()
    {
        CustomLogService::info(__FILE__, __LINE__,__CLASS__, 
            " start get all data ".$this->modal);

        CustomLogService::info(__FILE__, __LINE__,__CLASS__, 
            "get all base data ".$this->modal);

        return $this->sendResponse(
            $this->resource_index::collection(
                $this->modal::default_list(null,
                (new $this->modal)->get_order_array())->get()
            ), 200);
    }

    /** Search resoure storage.
     *
     * @return array
     */
    public function search(Request $request)
    {
        $input = $request->all();
        CustomLogService::info(__FILE__, __LINE__,__CLASS__, 
            " start search data ".$this->modal);

        if(isset($request['limit']) && $request['limit'] != null)
            $current_pagi = $request['limit'];
        else
            $current_pagi = $this->pagination;

        CustomLogService::info(__FILE__, __LINE__,__CLASS__, 
            "get all base data ".$this->modal);

        return $this->sendResponse(
            new $this->collection_index(
                $this->modal::default_search($input, $current_pagi, 
                (new $this->modal)->get_search_array(),
                (new $this->modal)->get_order_search($input)
                )), 200);
    }

    /** Search resoure storage.
     *
     * @return array
     */
    public function searchAll(Request $request)
    {
        $input = $request->all();
        CustomLogService::info(__FILE__, __LINE__,__CLASS__, 
            " start search all data ".$this->modal);

        CustomLogService::info(__FILE__, __LINE__,__CLASS__, 
            "get all base data ".$this->modal);

        return $this->sendResponse(
            $this->resource_index::collection(
                $this->modal::default_search($input, 999999, 
                (new $this->modal)->get_search_array(),
                (new $this->modal)->get_order_search($input)
                )), 200);
    }

    /** Display the specified resource in storage.
     *
     * @param $id
     *
     * @return array
     */
    public function show($id)
    {
        CustomLogService::info(__FILE__, __LINE__,__CLASS__, 
            "start show data by id: ".$id." ,modal: ".$this->modal);

        $object = $this->modal::getById($id);

        if ($object === false) {
            CustomLogService::error(__FILE__, __LINE__,__CLASS__, "get data by id error");
            return $this->sendErrorNotFound($this->no_object_error_msg);
        }

        CustomLogService::info(__FILE__, __LINE__,__CLASS__, "show data by id success");

        return $this->sendResponse(new $this->resource($object), 200);
    }

    /** Display the specified resource in storage by code.
     *
     * @param $id
     *
     * @return array
     */
    public function showByCode($code)
    {
        CustomLogService::info(__FILE__, __LINE__,__CLASS__, 
            "start show data by code: ".$code." ,modal: ".$this->modal);

        $object = $this->modal::getByCode((new $this->modal)->get_code_field(), $code);

        if ($object === false) {
            CustomLogService::error(__FILE__, __LINE__,__CLASS__, "get data by code error");
            return $this->sendErrorNotFound($this->no_object_error_msg);
        }

        CustomLogService::info(__FILE__, __LINE__,__CLASS__, "show data by code success");

        return $this->sendResponse(new $this->resource($object), 200);
    }

    /** Create the specified resource in storage.
     *
     * @param Request $request
     *
     * @return array
     */
    public function store(Request $request)
    {
        CustomLogService::info(__FILE__, __LINE__,__CLASS__, "start create new data");

        $input = $request->all();

        // prevent update field
        $input = $this->modal::prevent_update($input);

        $rule = (new $this->modal)->fieldSetValidate();

        $validator = Validator::make($input, (new $this->modal)->customValidate($request, $rule));
        
        if ($validator->fails()) {
            CustomLogService::error(__FILE__, __LINE__,__CLASS__, "validate data error");
            return $this->sendErrorBadRequest($validator->errors());
        }

        $user = Helper::getUserByJWTToken();
        if(empty($user)) {
            $input['created_by'] = 0; // 0 is system ID
            $input['updated_by'] = 0; // 0 is system ID
        }
        else {
            $input['created_by'] = $user['id'];
            $input['updated_by'] = $user['id'];
        }

        // check relation data before store
        $checked_data = $this->modal::check_store($input);
        if(!$checked_data['status'])
        {
            return $this->sendErrorBadRequest($checked_data['message']);
        }

        DB::beginTransaction();
        try {

            // change $input request after create data
            $changed_input = $this->modal::before_store($request, $input);

            // create data to database
            $field = $this->modal::default_store($changed_input, 
                (new $this->modal)->get_table_name(), (new $this->modal)->get_code_field());
        
            // call after created data
            $this->modal::after_store($field , $input, $request);
            DB::commit();
        } 
        catch (\Exception $ex) {
            DB::rollback();
            CustomLogService::error(__FILE__, __LINE__,__CLASS__, $ex->getMessage());
            return $this->sendErrorServerInternal($ex->getMessage());
        }
        
        CustomLogService::info(__FILE__, __LINE__,__CLASS__, "create new data success");

        return $this->sendResponse(new $this->resource($field), 201);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param                          $id
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function update($id, Request $request)
    {
        CustomLogService::info(__FILE__, __LINE__,__CLASS__, "start update data");

        $input = $request->all();

        // prevent update field
        $input = $this->modal::prevent_update($input);

        $rule = (new $this->modal)->fieldSetValidate($id);

        $validator = Validator::make($input, (new $this->modal)->customValidate($request, $rule));
        
        if ($validator->fails()) {
            CustomLogService::error(__FILE__, __LINE__,__CLASS__, "validate data error");
            return $this->sendErrorBadRequest($validator->errors());
        }

        $object = $this->modal::getById($id);

        if ($object === false) {
            CustomLogService::error(__FILE__, __LINE__,__CLASS__, "get data by id error");
            return $this->sendErrorNotFound($this->no_object_error_msg);
        }

        $user = Helper::getUserByJWTToken();
        if(empty($user)) {
            $object['updated_by'] = 0; // 0 is system ID
        }
        else {
            $object['updated_by'] = $user['id'];
        }

        // check relation data before update
        $checked_data = $this->modal::check_update($input, $object);
        if(!$checked_data['status'])
        {
            return $this->sendErrorBadRequest($checked_data['message'], $checked_data['code'] ?? null);
        }

        DB::beginTransaction();
        try {
            // change $input request after update data
            $changed_input = $this->modal::before_update($request, $input, $object);

            // update data base
            $updated_object = $this->modal::default_update($object, $changed_input, 
                (new $this->modal)->get_table_name(), (new $this->modal)->get_code_field());

            // call after updated database
            $this->modal::after_update($updated_object, $input, $request);
            DB::commit();
        } 
        catch (\Exception $ex) {
            DB::rollback();
            CustomLogService::error(__FILE__, __LINE__,__CLASS__, $ex->getMessage());
            return $this->sendErrorServerInternal($ex->getMessage());
        }

        CustomLogService::info(__FILE__, __LINE__,__CLASS__, "update data success");

        return $this->sendResponse(new $this->resource($object), 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $id
     *
     * @return array
     */
    public function destroy($id, Request $request)
    {
        $input = $request->all();

        CustomLogService::info(__FILE__, __LINE__,__CLASS__, "start delete data");

        $object = $this->modal::getById($id);

        if ($object === false) {
            CustomLogService::error(__FILE__, __LINE__,__CLASS__, "get data by id error");
            return $this->sendErrorBadRequest($this->update_version_msg, $this->modal::ERROR_VERSION_CODE);
        }
        
        $user = Helper::getUserByJWTToken();
        if(empty($user)) {
            $object['updated_by'] = 0; // 0 is system ID
        }
        else {
            $object['updated_by'] = $user['id'];
        }

        // check relation data before update
        $checked_data = $this->modal::check_delete($input, $object);
        if(!$checked_data['status'])
        {
            return $this->sendErrorBadRequest($checked_data['message'], $checked_data['code']);
        }

        DB::beginTransaction();
        try {
            $this->modal::default_delete($object, (new $this->modal)->get_table_name(), 
            (new $this->modal)->get_code_field());

            // call after updated database
            $this->modal::after_delete($object);

            DB::commit();
        } catch (\Exception $ex) {
            DB::rollback();
            CustomLogService::error(__FILE__, __LINE__,__CLASS__, $ex->getMessage());
            return $this->sendErrorServerInternal($ex->getMessage());
        }

        CustomLogService::info(__FILE__, __LINE__,__CLASS__, "deleted data success");

        return $this->sendResponse(new $this->resource($object), 200);
    }

    /** Export all data csv file 
     * 
     * @param string $name
     *
     * @return file
     */
    public function export(Request $request)
    {
        $input = $request->all();
        CustomLogService::info(__FILE__, __LINE__,__CLASS__, 
            " start export data ".$this->modal);

        // // check relation data before export
        // $checked_data = $this->modal::check_export($input);
        // if(!$checked_data['status'])
        // {
        //     return $this->sendErrorBadRequest($checked_data['message'], $checked_data['code'] ?? null);
        // }

        $export_data = $this->modal::default_export($input,
                            (new $this->modal)->get_search_array(),
                            (new $this->modal)->get_order_array());

        CustomLogService::info(__FILE__, __LINE__,__CLASS__, 
        "end export data ".$this->modal);
              
        return (new $this->form_export($export_data))
            ->download(Helper::getExportFileName($this->name), 
            \Maatwebsite\Excel\Excel::CSV, 
            [
            'Content-type' => 'application/csv;charset=UTF-8',
            'content-encoding' => 'UTF-8',
            'Access-Control-Expose-Headers' => 'Content-Disposition, X-Suggested-Filename'
        ]);
    }

    /** Export all data excel file
     *
     * @param Request $request
     *
     * @return file
     */
    public function exportExcel(Request $request)
    {
        $input = $request->all();
        CustomLogService::info(__FILE__, __LINE__,__CLASS__,
            " start export data ".$this->modal);

        // check relation data before export
        $checked_data = $this->modal::check_export($input);
        if(!$checked_data['status'])
        {
            return $this->sendErrorBadRequest($checked_data['message'], $checked_data['code'] ?? null);
        }

        $export_data = $this->modal::default_export($input,
            (new $this->modal)->get_search_array(),
            (new $this->modal)->get_order_array());

        CustomLogService::info(__FILE__, __LINE__,__CLASS__,
            "end export data ".$this->modal);

        return (new $this->form_excel_export($export_data))
            ->download(Helper::getExportExcelFileName($this->name),
                \Maatwebsite\Excel\Excel::XLSX,
                [
                    'Content-type' => '	application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;charset=UTF-8',
                    'content-encoding' => 'UTF-8',
                    'Access-Control-Expose-Headers' => 'Content-Disposition,X-Suggested-Filename'
                ]);
    }

    public function downloadPdf(Request $request)
    {
        CustomLogService::info(__FILE__, __LINE__,__CLASS__, "start download pdf file");

        if (empty($request->file_name) || empty($request->file_path)) {
            CustomLogService::error(__FILE__, __LINE__,__CLASS__, "empty file_name or file_path");
            return $this->sendErrorNotFound($this->no_object_error_msg);
        }

        $data = $this->modal::getS3FileInfo($request->file_path);
        if(empty($data)){
            CustomLogService::error(__FILE__, __LINE__,__CLASS__, "file_path error");
            return $this->sendErrorNotFound($this->no_object_error_msg);
        }

        $response =  [
            'Content-Type' => $data['mime'],
            'Content-Length' => $data['size'],
            'Content-Description' => 'File Transfer',
            'Content-Disposition' => "attachment; filename={$request->file_name}",
            'Content-Transfer-Encoding' => 'binary',
        ];

        ob_end_clean();

        CustomLogService::info(__FILE__, __LINE__,__CLASS__, "download pdf file success");

        return Download::make($data['file'], 200, $response);
    }

    /** Download all file from Amazon S3 and zip files
     * 
     * @param string $name
     *
     * @return file
     */
    public function downzip(Request $request)
    {
        CustomLogService::info(__FILE__, __LINE__,__CLASS__, "start download multiple file and zip file");
        
        if(!$this->modal::createZipFileS3($request, public_path(config('files.zip_file_dl')))) {
            CustomLogService::error(__FILE__, __LINE__,__CLASS__, "zip file empty or error");
            return $this->sendErrorNotFound($this->no_object_error_msg);
        }
 
        $response =  [
            'Content-Disposition' => "attachment; filename=".config('files.zip_file_dl'),
            'Content-Transfer-Encoding' => 'binary',
            'Content-type: application/zip'
        ];

        ob_end_clean();

        CustomLogService::info(__FILE__, __LINE__,__CLASS__, "download multiple file and zip file success");

        return response()->download(public_path(config('files.zip_file_dl')), 200, $response);
    }

    /** Upload multiple file
     * 
     * @param string $name
     *
     * @return file
     */
    public function multiUpload(Request $request)
    {
        CustomLogService::info(__FILE__, __LINE__,__CLASS__, "start upload file");

        $input = $request->all();

        $rule = (new $this->modal)->fieldSetValidate();

        $validator = Validator::make($input, (new $this->modal)->customValidate($request, $rule));
        
        if ($validator->fails()) {
            CustomLogService::error(__FILE__, __LINE__,__CLASS__, "validate data error");
            return $this->sendErrorBadRequest($validator->errors());
        }

        $user = Helper::getUserByJWTToken();
        if(empty($user)) {
            $input['created_by'] = 0; // 0 is system ID
            $input['updated_by'] = 0; // 0 is system ID
        }
        else {
            $input['created_by'] = $user['id'];
            $input['updated_by'] = $user['id'];
        }

        // check relation data before store
        $checked_data = $this->modal::check_upload($request);
        if(!$checked_data['status'])
        {
            return $this->sendErrorBadRequest($checked_data['message']);
        }

        DB::beginTransaction();
        try {
            foreach ($request->file('file') as $file) {
                $upload = $input;
                $upload['file'] = $file;
                // change $input request after create data
                $changed_input = $this->modal::before_upload($upload);

                // create data to database
                $field = $this->modal::default_store($changed_input, 
                    (new $this->modal)->get_table_name(), (new $this->modal)->get_code_field());
            
                // call after created data
                $this->modal::after_store($field , $upload, $request);
            }
            DB::commit();
        } 
        catch (\Exception $ex) {
            DB::rollback();
            CustomLogService::error(__FILE__, __LINE__,__CLASS__, $ex->getMessage());
            return $this->sendErrorServerInternal($ex->getMessage());
        }
        
        CustomLogService::info(__FILE__, __LINE__,__CLASS__, "upload multiple file success");

        return $this->sendResponse(new $this->resource($field), 201);
    }

    /** Import all data from csv file 
     * 
     * @param string $name
     *
     * @return file
     */
    public function importCSV(Request $request)
    {
        CustomLogService::info(__FILE__, __LINE__,__CLASS__, 
            " start import data ".$this->modal);   

        if(!$request->hasFile('file')){
            return $this->sendErrorBadRequest($this->not_import_file);
        }

        if($request->file('file')->getSize() > config('files.csv_max_size')) {
            return $this->sendErrorBadRequest($this->length_import_over_10mb);
        }

        if($this->modal::checkFormatImportCsv(fgets(fopen($request->file('file'), 'r'))) != true ){
            return $this->sendErrorBadRequest($this->import_format_failed);
        }

        $input = $request->all();

        $user = Helper::getUserByJWTToken();
        if(empty($user)) {
            $input['created_by'] = 0; // 0 is system ID
            $input['updated_by'] = 0; // 0 is system ID
        }
        else {
            $input['created_by'] = $user['id'];
            $input['updated_by'] = $user['id'];
        }
        
        $import =  (new $this->form_import);

        DB::beginTransaction();
        
        $import->import($request->file('file'));
        
        if($import->failures()->isNotEmpty()){
            DB::rollback();
            $failures = $import->failures();
            CustomLogService::error(__FILE__, __LINE__,__CLASS__, 
            "error import data ".$failures);
            return $this->sendErrorBadRequest($failures);
        }

        DB::commit();

        // save file csv
        $input =  (new $this->modal)::after_import($request, $input);

        CustomLogService::info(__FILE__, __LINE__,__CLASS__, 
            "sucess import data ".$this->modal);

        return $this->sendResponse(
            $this->resource_index::collection(
                $this->modal::default_list(null,
                (new $this->modal)->get_order_array())->get()
            ), 200);
    }

    public function downloadFileFromS3(Request $request)
    {
        $input = $request->all();

        $headers = [
            'Content-Type'        => 'Content-Type: application/jpeg',
            'Content-Disposition' => 'attachment; filename="'. $input['file_name'] .'"',
        ];

        return Download::make(Storage::disk('s3')->get($input['file_path']), 200, $headers);
    }
}

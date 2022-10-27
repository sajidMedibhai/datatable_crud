<?php

namespace App\Http\Controllers;

use App\Models\TaskHistoryModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use \Illuminate\Support\Facades\Validator;

class HomeController extends Controller
{
    public function index()
    {
        $data['title'] = 'Task';
        return view('welcome', $data);
    }

    public function task_history_list(Request $request)
    {
        try {
            $params['draw'] = $request->has('draw') ? $request->draw : 1;
            $results = get_task_history_data($request, false);
            $totalRecords = get_task_history_data($request, true);
            $sr_no = $request->has('start') ? $request->start : 0;

            $data_arry = [];

            foreach ($results as $key => $result) {
                $selected_id = $result->id;
                $data_arry[$key]['sr_no']       = ++$sr_no;


                $data_arry[$key]['check_box']   = "<input class='changed_status' data-current_id='" . $result->id . "' data-is_completed='" . $result->is_completed . "' type='checkbox' name='assign_case[]' value='" . $selected_id . "'>";

                if ($result->is_completed) {
                    $data_arry[$key]['check_box']   = "<input class='changed_status' data-current_id='" . $result->id . "' data-is_completed='" . $result->is_completed . "' type='checkbox' name='assign_case[]' value='" . $selected_id . "' checked>";
                }
                $data_arry[$key]['id']          = $result->id;
                $data_arry[$key]['description'] = $result->description;
                $data_arry[$key]['is_completed'] = ($result->is_completed == 1) ? "Yes" : "No";
                $data_arry[$key]['status']      = ($result->status == 1) ? "Active" : "In-Active";
                $data_arry[$key]['created_at']  = $result->created_at ? Carbon::parse($result->created_at)->format(UI_DATE_FORMAT) : "";


                $data_arry[$key]['action'] = "";
                $data_arry[$key]['action'] = $data_arry[$key]['action'] . "<a href='javascript:' data-name='" . ucwords($result->training_name) . "' data-delete='" . $selected_id . "' class='delete_item'><i class='fa red fa-trash'></i></a>&nbsp;&nbsp;&nbsp;";
            }

            return $data_arry;
            $json_data = array(
                "draw"            => intval($params['draw']),
                "recordsTotal"    => intval($totalRecords),
                "recordsFiltered" => intval($totalRecords),
                "data"            => $data_arry
            );
            return response()->json($json_data);
        } catch (\Exception $e) {
            Log::critical("HomeController::task_history_list");
            Log::critical($e);

            $data_arry = [];
            $json_data = array(
                "draw"            => 0,
                "recordsTotal"    => 0,
                "recordsFiltered" => 0,
                "data"            => $data_arry
            );
            return response()->json($json_data);
        }
    }

    public function add_task(Request $request)
    {
        $rules = [
            'description'   => 'required|unique:task_history,description',
        ];
        $messages = [
            'description.required' => 'This field is required.',
            'description.unique' => 'Task Already Exists.',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            $resposne['message'] = "Validation Error";
            return response()->json(["status" => true, "fields" => $validator->errors(), "message" => $resposne['message'],], 422);
        } else {

            $full_name = $request->first_name;

            if (isset($request->middle_name)) {
                $full_name .= " " . $request->middle_name;
            }

            if (isset($request->last_name)) {
                $full_name .= " " . $request->last_name;
            }

            $fields = array(
                'description'   => $request->description,
                'created_at'    => now(),
            );

            TaskHistoryModel::create($fields);

            return response()->json([
                'status' => true, 'message' => 'Account Created Successfully.'
            ], 200);
        }
    }

    public function delete_task(Request $request)
    {
        try {
            $rules = [
                'id' => 'required|exists:task_history,id',
            ];
            $messages = [
                'id.required' => 'ID is invalid',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                $resposne['message'] = "Validation Error";
                return response()->json(["status" => true, "fields" => $validator->errors(), "message" => $resposne['message'],], 422);
            } else {
                TaskHistoryModel::find($request->id)->delete();
                Log::info("Delete::record");
                Log::info($request->id);
                return response()->json([
                    'status' => true, 'message' => 'Record has been deleted successfully'
                ], 200);
            }
        } catch (\Exception $e) {
            Log::critical("HomeController::delete_task");
            Log::critical($e);
            return response()->json([
                'status' => true, 'message' => 'Unable to update record'
            ], 422);
        }
    }

    public function chnage_task_status(Request $request)
    {
        try {
            $rules = [
                'id'    => 'required|exists:task_history,id',
                'is_completed' => 'required',
            ];
            $messages = [
                'id.required' => 'ID is invalid',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                $resposne['message'] = "Validation Error";
                return response()->json(["status" => true, "fields" => $validator->errors(), "message" => $resposne['message'],], 422);
            } else {

                $update_filed = [
                    'is_completed' => $request->is_completed
                ];
                TaskHistoryModel::where('id', $request->id)->update($update_filed);

                return response()->json([
                    'status' => true, 'message' => 'Record has been updated successfully'
                ], 200);
            }
        } catch (\Exception $e) {
            Log::critical("HomeController::delete_task");
            Log::critical($e);
            return response()->json([
                'status' => true, 'message' => 'Unable to update record'
            ], 422);
        }
    }
}

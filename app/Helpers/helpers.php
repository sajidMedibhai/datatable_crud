<?php

use App\Models\TaskHistoryModel;

function get_task_history_data($request,  $limit = false)
{
    $start = $request->has('start') ? $request->start : 0;
    $length = $request->has('length') ? $request->length : 10;
    $params['draw'] = $request->has('draw') ? $request->draw : 1;

    $query = TaskHistoryModel::select('*');
    if (isset($request->search['value'])) {
        $query->where('task_history.description', 'like', '%' . $request->search['value'] . '%')
            ->orWhere('task_history.is_completed', 'like', '%' . $request->search['value'] . '%')
            ->orWhere('task_history.status', 'like', '%' . $request->search['value'] . '%');
    }

    $query->orderBy('task_history.id', 'DESC');

    if ($limit) {
        return $query
            ->get()
            ->count();
    } else {
        return $query
            // ->offset($start)
            // ->limit($length)
            ->get();
    }
}

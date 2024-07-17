<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreQueryRequest;
use App\Http\Requests\UpdateQueryRequest;
use App\Models\Outputting;
use App\Models\Query;
use App\Models\Recording;
use Illuminate\Http\Request;
use App\Http\Resources\QueryResource;
use App\Http\Resources\RecordingResource;
use App\Http\Resources\ScheduleResource;
use App\Http\Resources\OutputtingResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Services\GoogleService;

class QueryController extends Controller
{
    protected $recordingController;

    public function __construct(RecordingController $recordingController)
    {
        $this->recordingController = $recordingController;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // $queries = Query::all();
        $name = $request->query('name');
        $createdBy = $request->query('created_by');
        $updatedBy = $request->query('updated_by');

        $queries = Query::when($name, function ($query, $name) {
            return $query->where('name', 'like', '%' . $name . '%');
        })
        ->when($createdBy, function ($query, $createdBy) {
            return $query->whereHas('createdBy', function ($q) use ($createdBy) {
                $q->where('name', $createdBy);
            });
        })
        ->when($updatedBy, function ($query, $updatedBy) {
            return $query->whereHas('updatedBy', function ($q) use ($updatedBy) {
                $q->where('name', $updatedBy);
            });
        })
        ->orderBy('updated_at', 'desc')
        ->paginate(10)
        ->onEachSide(1);

        return inertia('Query/Index', [
            'queries' => QueryResource::collection($queries),
            'queryParams' => $request->query() ?: null,
            'success' => session('success'),
            'fail' => session('fail'),

        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $tableDetails = $this->getTableDetails();
        return inertia('Query/Create', [
            'tables' => $tableDetails,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreQueryRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();
        $data['client_id'] = Auth::user()->client_id; // 假設用戶關聯了客戶端

        try {
            $query = new Query();
            $query->fill($data)->save();
            $this->recordingController->recordQueryExecution(new Request(), $query->id);
        } catch (\Exception $e) {
            \Log::error('Error creating query:', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return to_route('query.index')->with('fail', 'Error creating the query.');
        }
        return to_route('query.show', ['id' => $query->id])->with('success', 'Query was created');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $query = Query::findOrFail($id);
        $recordings = $query->recordings()->orderBy('updated_at', 'desc')->paginate(10)->onEachSide(1);
        $outputtings = $query->outputtings()->orderBy('updated_at', 'desc')->get();
        $schedules = $query->schedules()->orderBy('updated_at', 'desc')->get();
        return inertia('Query/Show', [
            'query' => new QueryResource($query),
            'recordings' => RecordingResource::collection($recordings),
            'outputtings' => OutputtingResource::collection($outputtings),
            'schedules' => ScheduleResource::collection($schedules),
            'currentDatabase' => request()->get('current_connection'),
            'success' => session('success'),
            'fail' => session('fail'),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $query = Query::findOrFail($id);
        $tableDetails = $this->getTableDetails();

        return inertia('Query/Edit', [
            'tables' => $tableDetails,
            'query' => new QueryResource($query),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateQueryRequest $request, $id)
    {
        $data = $request->validated();
        $data['updated_by'] = Auth::id();

        try {
            $query = Query::findOrFail($id);
            $query->update($data);
            $query->refresh();
            $this->recordingController->recordQueryExecution(new Request(), $query->id);
        } catch (\Exception $e) {
            \Log::error('Error updating query:', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return to_route('query.show', ['id' => $id])->with('fail', 'Error updating the query.');
        }

        return to_route('query.show', ['id' => $query->id])->with('success', 'Query was updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        \Log::info('Query start to delete', ['query'=> $id]);
        $query = Query::findOrFail($id);
        $name = $query->name;
        $query->delete();
        return to_route('query.index')->with('success', "Query \" $name \" was deleted");
    }

    private function getTableDetails()
    {
        $tables = DB::select('SHOW TABLES');
        $databaseName = DB::getDatabaseName();
        $tablePrefix = 'Tables_in_' . $databaseName;

        $tableDetails = [];
        foreach ($tables as $table) {
            $tableName = $table->$tablePrefix;
            $columns = DB::select("SHOW COLUMNS FROM {$tableName}");

            $columnDetails = array_map(function($column) {
                return [
                    'name' => $column->Field,
                    'type' => $column->Type,
                ];
            }, $columns);

            $tableDetails[$tableName] = $columnDetails;
        }

        return $tableDetails;
    }

    public function verify(Request $request)
    {
        $querySql = $request->input('query_sql');

        // 確保 querySql 是字符串類型
        if (!is_string($querySql)) {
            throw new \InvalidArgumentException('Invalid SQL query');
        }
        if (!preg_match('/^\s*SELECT/i', $querySql)) {
            throw new \InvalidArgumentException('Only SELECT queries are allowed');
        }

        try {
            $result = DB::select($querySql);
            return response()->json(['result' => $result]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}

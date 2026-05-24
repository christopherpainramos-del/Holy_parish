<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Baptism;
use App\Models\Communion;
use App\Models\Confirmation;
use App\Models\Wedding;
use App\Models\Funeral;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Schema;

class RecordController extends Controller
{
    /**
     * Display the list of record categories (Books) or dynamic listings.
     */
    public function index(Request $request)
    {
        $category = $request->query('category');
        $book_number = $request->query('book_number');

        if ($category && $book_number !== null) {
            $title = strtoupper($category) . ' BOOK ' . $book_number;
            $records = collect(); // Default empty collection

            switch (strtolower($category)) {
                case 'baptism':
                    $records = Baptism::where('book_number', $book_number)->get();
                    break;
                case 'communion':
                    $records = Communion::where('book_number', $book_number)->get();
                    break;
                case 'confirmation':
                    $records = Confirmation::where('book_number', $book_number)->get();
                    break;
                case 'wedding':
                    $records = Wedding::where('book_number', $book_number)->get();
                    break;
                case 'funeral':
                    $records = Funeral::where('book_number', $book_number)->get();
                    break;
            }

            // Maps dynamic paths to records.{category}_details.blade.php
            $viewName = 'records.' . strtolower($category) . '_details';

            if (!view()->exists($viewName)) {
                abort(404, "The layout configuration [{$viewName}.blade.php] for this registry was not found.");
            }

            return view($viewName, [
                'category' => $category,
                'bookNumber' => $book_number,
                'title' => $title,
                'records' => $records, 
            ]);
        }

        // If only category is provided, show the volume shelf.
        if ($category) {
            $title = strtoupper($category) . " BOOK";
            $volumes = range(1, 24);
            return view('records.volumes', compact('volumes', 'category', 'title'));
        }

        $books = [
            ['title' => 'BAPTISM', 'category' => 'baptism', 'file' => 'baprec.png'],
            ['title' => 'COMMUNION', 'category' => 'communion', 'file' => 'comrec.png'],
            ['title' => 'CONFIRMATION', 'category' => 'confirmation', 'file' => 'conrec.png'],
            ['title' => 'WEDDING', 'category' => 'wedding', 'file' => 'wedrec.png'],
            ['title' => 'FUNERAL', 'category' => 'funeral', 'file' => 'funrec.png'],
        ];

        return view('records.index', compact('books'));
    }

    /**
     * Show the form for creating a new record.
     */
    public function create(Request $request)
    {
        $category = $request->query('category', 'Baptism');
        $book_number = $request->query('book_number', 1);

        return view('records.create', compact('category', 'book_number'));
    }

    /**
     * Display the certificate template for baptism records.
     */
    public function showBaptism($id)
    {
        $record = Baptism::findOrFail($id);
        return view('records.baptism_certificate', compact('record'));
    }

    /**
     * Display the certificate template for communion records.
     */
    public function showCommunion($id)
    {
        $record = Communion::findOrFail($id);
        return view('records.communion_certificate', compact('record'));
    }

    /**
     * Display the certificate template for confirmation records.
     */
    public function showConfirmation($id)
    {
        $record = Confirmation::findOrFail($id);
        return view('records.confirmation_certificate', compact('record'));
    }

    /**
     * Display the certificate template for wedding records.
     */
    public function showWedding($id)
    {
        $record = Wedding::findOrFail($id);
        return view('records.wedding_certificate', compact('record'));
    }

    /**
     * Display the certificate template for funeral records.
     */
    public function showFuneral($id)
    {
        $record = Funeral::findOrFail($id);
        return view('records.funeral_certificate', compact('record'));
    }

    /**
     * Display record details (generic fallback).
     */
    public function show($id, Request $request)
    {
        // Get category from query parameter or route
        $category = $request->query('category', 'baptism');
        
        switch (strtolower($category)) {
            case 'baptism':
                $record = Baptism::findOrFail($id);
                return view('records.show', compact('record', 'category'));
            case 'communion':
                $record = Communion::findOrFail($id);
                return view('records.show', compact('record', 'category'));
            case 'confirmation':
                $record = Confirmation::findOrFail($id);
                return view('records.show', compact('record', 'category'));
            case 'wedding':
                $record = Wedding::findOrFail($id);
                return view('records.show', compact('record', 'category'));
            case 'funeral':
                $record = Funeral::findOrFail($id);
                return view('records.show', compact('record', 'category'));
            default:
                abort(404, 'Record category not found');
        }
    }

    /**
     * Show edit form for a record.
     */
    public function edit($id, Request $request)
    {
        $category = $request->query('category', 'baptism');
        $book_number = $request->query('book_number', 1);
        
        switch (strtolower($category)) {
            case 'baptism':
                $record = Baptism::findOrFail($id);
                break;
            case 'communion':
                $record = Communion::findOrFail($id);
                break;
            case 'confirmation':
                $record = Confirmation::findOrFail($id);
                break;
            case 'wedding':
                $record = Wedding::findOrFail($id);
                break;
            case 'funeral':
                $record = Funeral::findOrFail($id);
                break;
            default:
                abort(404, 'Record category not found');
        }
        
        return view('records.edit', compact('record', 'category', 'book_number'));
    }

    /**
     * Update a record in the database.
     */
    public function update(Request $request, $id)
    {
        $category = strtolower($request->category ?? '');
        
        // Validation rules
        $validationRules = [
            'category'    => 'required|string',
            'book_number' => 'required|integer',
        ];
        
        if ($category === 'baptism') {
            $validationRules['candidate_name'] = 'required|string|max:255';
        } elseif ($category === 'wedding') {
            $validationRules['year']        = 'required|string';
            $validationRules['month_day']   = 'required|string';
            $validationRules['groom_name']  = 'required|string|max:255';
            $validationRules['groom_age']   = 'required|integer';
            $validationRules['bride_name']  = 'required|string|max:255';
            $validationRules['bride_age']   = 'required|integer';
        } elseif (in_array($category, ['communion', 'confirmation'])) {
            $validationRules['first_name']  = 'required|string|max:255';
            $validationRules['last_name']   = 'required|string|max:255';
        }
        
        $request->validate($validationRules);
        
        // Find and update the record
        switch ($category) {
            case 'baptism':      
                $record = Baptism::findOrFail($id);
                break;
            case 'communion':    
                $record = Communion::findOrFail($id);
                break;
            case 'confirmation': 
                $record = Confirmation::findOrFail($id);
                break;
            case 'wedding':      
                $record = Wedding::findOrFail($id);
                break;
            case 'funeral':      
                $record = Funeral::findOrFail($id);
                break;
            default: 
                abort(400, 'Invalid record category reference');
        }
        
        // Prepare update data
        $updateData = [];
        
        // Common fields
        if ($request->has('book_number')) $updateData['book_number'] = $request->book_number;
        if ($request->has('page_number')) $updateData['page_number'] = $request->page_number;
        if ($request->has('line_number')) $updateData['line_number'] = $request->line_number;
        
        // Category-specific fields
        switch ($category) {
            case 'baptism':
                $updateData['candidate_name'] = $request->candidate_name;
                $updateData['legitimacy'] = $request->legitimacy;
                $updateData['birth_date'] = $request->birth_date;
                $updateData['birth_place'] = $request->birth_place;
                $updateData['baptism_date'] = $request->baptism_date;
                $updateData['godfather'] = $request->godfather;
                $updateData['godmother'] = $request->godmother;
                $updateData['minister_name'] = $request->minister_name;
                $updateData['parents'] = $request->parents;
                $updateData['remarks'] = $request->remarks;
                break;
            case 'communion':
                $updateData['first_name'] = $request->first_name;
                $updateData['last_name'] = $request->last_name;
                $updateData['communion_date'] = $request->communion_date;
                $updateData['minister_name'] = $request->minister_name;
                $updateData['remarks'] = $request->remarks;
                break;
            case 'confirmation':
                $updateData['first_name'] = $request->first_name;
                $updateData['last_name'] = $request->last_name;
                $updateData['year'] = $request->year;
                $updateData['month_day'] = $request->month_day;
                $updateData['age'] = $request->age;
                $updateData['minister_name'] = $request->minister_name;
                $updateData['remarks'] = $request->remarks;
                break;
            case 'wedding':
                $updateData['groom_name'] = $request->groom_name;
                $updateData['groom_age'] = $request->groom_age;
                $updateData['groom_status'] = $request->groom_status;
                $updateData['groom_residence'] = $request->groom_residence;
                $updateData['groom_parents'] = $request->groom_parents;
                $updateData['bride_name'] = $request->bride_name;
                $updateData['bride_age'] = $request->bride_age;
                $updateData['bride_status'] = $request->bride_status;
                $updateData['bride_residence'] = $request->bride_residence;
                $updateData['bride_parents'] = $request->bride_parents;
                $updateData['year'] = $request->year;
                $updateData['month_day'] = $request->month_day;
                $updateData['minister_name'] = $request->minister_name;
                $updateData['remarks'] = $request->remarks;
                break;
            case 'funeral':
                $updateData['deceased_name'] = $request->deceased_name;
                $updateData['death_date'] = $request->death_date;
                $updateData['burial_date'] = $request->burial_date;
                $updateData['minister_name'] = $request->minister_name;
                $updateData['remarks'] = $request->remarks;
                break;
        }
        
        $record->update($updateData);
        
        return redirect()->route('records.index', [
            'category' => $request->category,
            'book_number' => $request->book_number
        ])->with('success', 'Record successfully updated!');
    }
    
    /**
     * Delete a record from the database.
     */
    public function destroy($id, Request $request)
    {
        $category = strtolower($request->query('category', 'baptism'));
        $book_number = $request->query('book_number', 1);
        
        try {
            switch ($category) {
                case 'baptism':      
                    $record = Baptism::findOrFail($id);
                    break;
                case 'communion':    
                    $record = Communion::findOrFail($id);
                    break;
                case 'confirmation': 
                    $record = Confirmation::findOrFail($id);
                    break;
                case 'wedding':      
                    $record = Wedding::findOrFail($id);
                    break;
                case 'funeral':      
                    $record = Funeral::findOrFail($id);
                    break;
                default: 
                    abort(400, 'Invalid record category reference');
            }
            
            $record->delete();
            
            return redirect()->route('records.index', [
                'category' => $category,
                'book_number' => $book_number
            ])->with('success', 'Record successfully deleted!');
            
        } catch (\Exception $e) {
            return redirect()->route('records.index', [
                'category' => $category,
                'book_number' => $book_number
            ])->with('error', 'Error deleting record: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created sacramental record in the database.
     */
    public function store(Request $request)
    {
        $category = strtolower($request->category ?? '');

        // 1. DYNAMIC INPUT VALIDATION PARSING
        $validationRules = [
            'category'    => 'required|string',
            'book_number' => 'required|integer',
        ];

        // Apply strict dynamic rules according to your specific blade form schemas
        if ($category === 'baptism') {
            $validationRules['candidate_name'] = 'required|string|max:255';
        } elseif ($category === 'wedding') {
            $validationRules['book_number'] = 'required|integer';
            $validationRules['page_number'] = 'required|integer';
            $validationRules['line_number'] = 'required|integer';
            $validationRules['year']        = 'required|string';
            $validationRules['month_day']   = 'required|string';
            $validationRules['groom_name']  = 'required|string|max:255';
            $validationRules['groom_age']   = 'required|integer';
            $validationRules['bride_name']  = 'required|string|max:255';
            $validationRules['bride_age']   = 'required|integer';
        } elseif (in_array($category, ['communion', 'confirmation'])) {
            $validationRules['page_number'] = 'required|integer';
            $validationRules['line_number'] = 'required|integer';
            $validationRules['first_name']  = 'required|string|max:255';
            $validationRules['last_name']   = 'required|string|max:255';
            
            if ($category === 'confirmation') {
                $validationRules['year']      = 'required|string';
                $validationRules['month_day'] = 'required|string';
                $validationRules['age']       = 'required|integer';
            }
        }

        $request->validate($validationRules);

        // 2. PARSE BOOK, PAGE, AND LINE CONFIGURATIONS
        $bookNumber = (int) $request->book_number;
        $pageNumber = $request->has('page_number') ? (int) $request->page_number : 1;
        $lineNumber = $request->has('line_number') ? (int) $request->line_number : 1;

        // 3. COMPILE STRUCTURAL DATA MAPPING
        $payload = [
            'category'           => $request->category,
            'book_number'        => $bookNumber,
            'page_number'        => $pageNumber,
            'line_number'        => $lineNumber,
            
            // Normalized names mapping safely over different schemas
            'first_name'         => $request->first_name ?? ($request->candidate_name ?? ''),
            'last_name'          => $request->last_name ?? '',
            'candidate_name'     => $request->candidate_name ?? trim(($request->first_name ?? '') . ' ' . ($request->last_name ?? '')),
            
            // Baptism specific fields
            'legitimacy'         => $request->legitimacy ?? '',
            'birth_date'         => $request->birth_date ?? ($category === 'baptism' ? now()->toDateString() : null),
            'birth_place'        => $request->birth_place ?? '',
            'father_birthplace'  => $request->father_birthplace ?? '',
            'mother_birthplace'  => $request->mother_birthplace ?? '',
            'baptism_date'       => $request->baptism_date ?? ($category === 'baptism' || $category === 'communion' ? now()->toDateString() : null),
            'godfather'          => $request->godfather ?? '',
            'godmother'          => $request->godmother ?? '',
            'remarks'            => $request->remarks ?? '',
            
            // Shareable general structural fields
            'residence'          => $request->residence ?? '',
            'minister_name'      => $request->minister_name ?? '',
            'parents'            => $request->parents ?? '',
            'sponsors'           => $request->sponsors ?? '',
            
            // Communion specific fields
            'communion_date'     => $request->communion_date ?? ($category === 'communion' ? now()->toDateString() : null),
            'place_of_baptism'   => $request->place_of_baptism ?? '',
            
            // Confirmation / Wedding unified metrics
            'year'               => $request->year ?? now()->format('Y'),
            'month_day'          => $request->month_day ?? now()->format('m/d'),
            'age'                => $request->age ?? 0,
            'father_name'        => $request->father_name ?? '',
            'parents_residence'  => $request->parents_residence ?? '',
            
            // Cross-compatible structural keys
            'mother_name'        => $request->mother_name ?? ($request->mother_maiden_name ?? ''),
            'mother_maiden_name' => $request->mother_maiden_name ?? ($request->mother_name ?? ''),
            'birthplace'         => $request->birthplace ?? ($request->birth_place ?? ''),
            
            // Wedding specific fields
            'groom_name'              => $request->groom_name ?? '',
            'groom_age'               => $request->groom_age ?? 0,
            'groom_status'            => $request->groom_status ?? '',
            'groom_residence'         => $request->groom_residence ?? '',
            'groom_parents'           => $request->groom_parents ?? '',
            'groom_parents_residence' => $request->groom_parents_residence ?? '',
            'bride_name'              => $request->bride_name ?? '',
            'bride_age'               => $request->bride_age ?? 0,
            'bride_status'            => $request->bride_status ?? '',
            'bride_residence'         => $request->bride_residence ?? '',
            'bride_parents'           => $request->bride_parents ?? '',
            'bride_parents_residence' => $request->bride_parents_residence ?? '',
            
            // Funeral fallbacks
            'death_date'         => $request->death_date ?? now()->toDateString(),
            'burial_date'        => $request->burial_date ?? now()->toDateString(),
            'deceased_name'      => $request->deceased_name ?? ($request->candidate_name ?? ''),
        ];

        // Always exclude custom layout variables that never belong in any database table
        $exclusions = ['bk_pg_ln'];

        // 4. RESOLVE MODEL CONTEXT DYNAMICALLY
        switch ($category) {
            case 'baptism':      $model = new Baptism(); break;
            case 'communion':    $model = new Communion(); break;
            case 'confirmation': $model = new Confirmation(); break;
            case 'wedding':      $model = new Wedding(); break;
            case 'funeral':      $model = new Funeral(); break;
            default: abort(400, 'Invalid record category reference');
        }

        // Filter payload array: keep only columns that exist on the model's actual table
        $finalData = [];
        foreach ($payload as $key => $value) {
            if (Schema::hasColumn($model->getTable(), $key)) {
                $finalData[$key] = $value;
            }
        }

        // Extra fallback check to drop structural helper strings if missing
        $dbPayload = Arr::except($finalData, $exclusions);

        // 5. SECURELY INSERT DATA INTO TARGET SYSTEM REGISTRY
        $model->fill($dbPayload)->save();

        // 6. REDIRECT DIRECTLY TO THE UPDATED BOOK SERIES VIEW WITH FLASH MESSAGE
        return redirect()->route('records.index', [
            'category' => $request->category,
            'book_number' => $bookNumber
        ])->with('success', 'Record successfully saved!');
    }
}
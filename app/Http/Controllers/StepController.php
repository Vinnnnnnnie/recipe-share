<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Step;
use App\Http\Requests\StoreStepsRequest;
use App\Http\Requests\UpdateStepsRequest;

class StepController extends Controller {
    /**
     * Display a listing of the resource.
     */
    public function index(): void {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): void {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Step $step): void {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Step $step): void {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Step $step): void {
        //
    }
}

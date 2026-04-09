<?php

namespace App\Http\Controllers\TVS;

use App\Http\Controllers\Controller;

class TvsWebController extends Controller
{
    public function dashboard()
    {
        return view('tvs.dashboard', ['title' => 'TVS Service – Dashboard']);
    }

    public function vehicles()
    {
        return view('tvs.vehicles.index', ['title' => 'Vehicle Management']);
    }

    public function vehicleShow($id)
    {
        return view('tvs.vehicles.show', ['title' => 'Vehicle Detail', 'vehicleId' => $id]);
    }

    public function parties()
    {
        return view('tvs.parties.index', ['title' => 'Customer / Party Management']);
    }

    public function partyCreate()
    {
        return view('tvs.parties.create', ['title' => 'New Customer / Party']);
    }

    public function partyShow($id)
    {
        return view('tvs.parties.show', ['title' => 'Party Detail', 'partyId' => $id]);
    }

    public function jobCards()
    {
        return view('tvs.job-cards.index', ['title' => 'Job Cards']);
    }

    public function jobCardCreate()
    {
        return view('tvs.job-cards.create', ['title' => 'New Job Card – Reception']);
    }

    public function jobCardShow($id)
    {
        return view('tvs.job-cards.show', ['title' => 'Job Card Detail', 'jobCardId' => $id]);
    }

    public function gatePasses()
    {
        return view('tvs.gate-passes.index', ['title' => 'Gate Pass Management']);
    }

    public function warranties()
    {
        return view('tvs.warranties.index', ['title' => 'Warranty Management']);
    }

    public function reports()
    {
        return view('tvs.reports.index', ['title' => 'Reports & Analytics']);
    }
}

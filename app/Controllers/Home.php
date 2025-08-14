<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {

        $session = session();
        $user = null;
        $isLoggedIn = false;

        if ($session->get('user_id')) {
            $userModel = new \App\Models\UserModel();
            $user = $userModel->find($session->get('user_id'));
            $isLoggedIn = true;
        }


        $layananModel = new \App\Models\LayananModel();


        $services = $layananModel->where('status', 'aktif')->findAll();


        $groupedServices = [
            'motor' => [],
            'mobil' => [],
            'lainnya' => []
        ];

        foreach ($services as $service) {
            $vehicleType = strtolower($service['jenis_kendaraan']);
            if (isset($groupedServices[$vehicleType])) {
                $groupedServices[$vehicleType][] = $service;
            }
        }


        $userModel = new \App\Models\UserModel();
        $bookingModel = new \App\Models\BookingModel();
        $transaksiModel = new \App\Models\TransaksiModel();

        $stats = [
            'total_customers' => $userModel->where('role', 'pelanggan')->countAllResults(),
            'total_services' => $layananModel->where('status', 'aktif')->countAllResults(),
            'total_bookings' => $bookingModel->countAllResults(),
            'total_transactions' => $transaksiModel->countAllResults()
        ];

        $data = [
            'title' => 'TiaraWash - Layanan Cuci Kendaraan Premium',
            'services' => $services,
            'grouped_services' => $groupedServices,
            'stats' => $stats,
            'user' => $user,
            'isLoggedIn' => $isLoggedIn
        ];

        return view('welcome_message', $data);
    }

    public function booking()
    {

        $layananModel = new \App\Models\LayananModel();
        $pelangganModel = new \App\Models\PelangganModel();


        $services = $layananModel->where('status', 'aktif')->findAll();


        $groupedServices = [];
        foreach ($services as $service) {
            $groupedServices[$service['jenis_kendaraan']][] = $service;
        }


        $user = null;
        $pelanggan = null;
        $isLoggedIn = false;

        if (session()->get('logged_in') && session()->get('role') === 'pelanggan') {
            $userModel = new \App\Models\UserModel();
            $user = $userModel->find(session()->get('user_id'));
            $pelanggan = $pelangganModel->where('user_id', session()->get('user_id'))->first();
            $isLoggedIn = true;
        }

        $data = [
            'title' => 'Booking Layanan - TiaraWash',
            'services' => $services,
            'grouped_services' => $groupedServices,
            'user' => $user,
            'pelanggan' => $pelanggan,
            'isLoggedIn' => $isLoggedIn
        ];

        return view('booking/form', $data);
    }
}

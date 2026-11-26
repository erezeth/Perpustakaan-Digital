<?php
declare(strict_types=1);

final class HomeController extends Controller
{
    public function index(): void
    {
        if (!Auth::check()) {
            $this->redirect('/login');
        }

        $this->redirect(Auth::role() === 'petugas' ? '/admin' : '/beranda');
    }
}

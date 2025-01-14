<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Interfaces\UserRepositoryInterface;

class LoginController extends Controller
{
    /**
     * Instancia del repositorio de usuarios.
     * @var UserRepositoryInterface
     */
    protected $userRepository;

    /**
     * Inyección de dependencias mediante el constructor.
     *
     * @param UserRepositoryInterface $userRepository
     */
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Muestra la vista de login.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Procesa el formulario de login.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        // Validamos los datos del formulario
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        // Obtenemos el usuario por email
        $user = $this->userRepository->findByEmail($request->input('email'));

        // Verificamos que exista y que la contraseña sea correcta
        if ($user && Hash::check($request->input('password'), $user->password)) {
            // Iniciamos la sesión del usuario
            Auth::login($user);
            // Redirigimos a la página de inicio (o la que desees)
            return redirect('/users')->with('success', '¡Has iniciado sesión correctamente!');
        }

        // Si no coincide, regresamos con un mensaje de error
        return redirect()->back()->withErrors(['error' => 'Credenciales inválidas.']);
    }

    /**
     * Cierra la sesión del usuario.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout()
    {
        Auth::logout();
        return redirect()->route('login.show')->with('success', '¡Sesión cerrada correctamente!');
    }
}

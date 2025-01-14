<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Interfaces\UserRepositoryInterface;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    /**
     * Instancia del repositorio de usuarios.
     * @var UserRepositoryInterface
     */
    protected $userRepository;

    /**
     * Inyectamos nuestro repositorio para manejar la creación de usuarios.
     *
     * @param UserRepositoryInterface $userRepository
     */
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Muestra la vista de registro de usuarios.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Procesa el formulario de registro.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function register(Request $request)
    {
        // Validamos los datos del formulario
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            // password_confirmation debe estar en el form
        ]);

        // Creamos el usuario usando el repositorio
        $user = $this->userRepository->createUser([
            'name'     => $request->input('name'),
            'email'    => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'api_token' => Str::random(60)
        ]);

        $account = Account::create([
            'user_id' => $user->id,
            'balance' => 0.00
        ]);

        return redirect()->route('login')->with('success', '¡Usuario registrado!');
    }
}

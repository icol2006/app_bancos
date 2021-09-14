<?php

namespace App\Http\Controllers\Cuenta;

use App\Http\Controllers\Controller;
use App\Models\Cuenta;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function index(Request $request)
    {
        $listado_cuentas = [];
        $usuarios =  User::all();
        $usuarioID = $request['usuarioID'];

        try{
            $listado_cuentas = Cuenta::where('usuarioID', $usuarioID)->get();
        }catch (Exception $ex) {
        }

        $data = [
            'usuarios'  => $usuarios,
            'listado_cuentas'  => $listado_cuentas,
            'usuarioSelecionado'  => $usuarioID,
        ];

        return view("cuenta.index", compact('data'));
    }

    public function edit($id)
    {
        $cuenta = Cuenta::find($id);
        $usuarios =  User::all();

        $data = [
            'cuenta'  => $cuenta,
            'usuarios'  => $usuarios,
        ];

        return view('cuenta.edit', compact('data'));
    }

    public function create()
    {
        $usuarios =  User::all();

        $data = [
            'usuarios'  => $usuarios,
        ];
        return view('cuenta.create', compact('data'));
    }

    public function update(Request $request, $id)
    {
        $this->validate(
            $request,
            [
                'usuarioID'     => 'required',
                'monto'     => 'required',
                'tipo'     => 'required',
            ],
            [
                'usuarioID.required'      => 'Usuario es requerida',
                'monto.required'      => 'Monto es requerido',
                'tipo.required'      => 'Tipo  es requerido',
            ]
        );

        $data = Cuenta::find($id);
        $data->update($request->all());

        return redirect('cuenta/')->with('success', 'Datos guardados!');
    }

    public function store(Request $request)
    {
        $this->validate(
            $request,
            [
                'usuarioID'     => 'required',
                'monto'     => 'required',
                'tipo'     => 'required',
            ],
            [
                'usuarioID.required'      => 'Usuario es requerida',
                'monto.required'      => 'Monto es requerido',
                'tipo.required'      => 'Tipo  es requerido',
            ]
        );

        $input = $request->all();

        $data = Cuenta::create($input);

        return redirect('cuenta/')->with('success', 'Datos guardados!');
    }


    public function delete($id)
    {
        try {
            $datos = Cuenta::find($id);
            $datos->delete();
        } catch (\Illuminate\Database\QueryException $e) {

            if ($e->getCode() == "23000") { //23000 is sql code for integrity constraint violation
                $errors = array();
                array_push($errors, 'No se puede borrar el registro. Esta siendo usado en la base de datos');
                return redirect()->route('cuenta.index')
                    ->with('errors', $errors);
            }
        }

        return redirect()->route('cuenta.index')
            ->with('success', 'Registro borrado exitosamente');
    }

    public function api_getAll()
    {
        $cuentas = Cuenta::all();

        return response($cuentas, 200);
    }

    public function api_getAllByUsuarioID(Request $request, $id)
    {
        $data = [];
        if (Cuenta::where('usuarioID', $id)->exists()) {
            $data = Cuenta::where('usuarioID', $id)->get()->toJson(JSON_PRETTY_PRINT);
        }
        return response($data, 200);
    }

    public function api_getById(Request $request, $id)
    {
        $data = new Cuenta();
        if (Cuenta::where('id', $id)->exists()) {
            $data = Cuenta::where('id', $id)->get()->first()->toJson(JSON_PRETTY_PRINT);
        }
        return response($data, 200);
    }

    public function api_add(Request $request)
    {
        $input = $request->all();
        $data = Cuenta::create($input);

        return response($data, 200);
    }
}

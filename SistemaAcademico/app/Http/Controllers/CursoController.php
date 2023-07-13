<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use Illuminate\Http\Request;
use App\Models\Docente;
use App\Models\Matricula;
use App\Models\Curso;
use App\Models\Grado;
use App\Models\Bimestre;
use App\Models\NotaBimestre;
use Illuminate\Support\Facades\DB;

class CursoController extends Controller
{
    public function index($coddocente)
    {
        if (session('logged')) {
            $docente = Docente::where('codigo_docente', $coddocente)->first();
            $datos= $docente->nombre1_docente." ".$docente->apellido1_docente." ".$docente->apellido2_docente;

            if ($docente) {

                $cursos = $docente->cursos;
                $title= 'Lista Cursos';

                $bimestres = Bimestre::all();

                return view('academico.curso', 
                compact(
                    'cursos',
                    'title',
                    'coddocente',
                    'datos',
                    'bimestres'
                ));

            } else {
                return redirect('/')->withErrors(['ERROR' => 'No se encontró el docente']);
            }
        } else {
            return redirect('/')->withErrors(['ERROR' => 'Debes logearte primero']);
        }
    }

    public function matricula($coddocente,$codcurso, $codbimestre){
        if (session('logged')) {
            $docente = Docente::where('codigo_docente', $coddocente)->first();
            $datos = $docente->nombre1_docente . " " . $docente->apellido1_docente . " " . $docente->apellido2_docente;
            
            if ($docente) {
                $matricula = Matricula::where('codigo_docente', $coddocente)->first();
                $codmatricula = $matricula->codigo_matricula;
        
                if ($matricula) {
                    $curso = Curso::where('codigo_curso', $codcurso)->first();
                    $nomcurso = $curso->nombre_curso;
                    $codcurso = $curso->codigo_curso;
        
                    $grado = Grado::where('codigo_grado', $matricula->codigo_grado)->first();
                    $nomgrado = $grado->nombre_grado . " " . $grado->seccion_grado;

                    $bimestre = Bimestre::where('codigo_bimestre', $codbimestre)->first();
                    $nombimestre = $bimestre->nombre_bimestre;
                    $codbimestre = $bimestre->codigo_bimestre;

                    $notas = DB::table('alumnos AS a')
                    ->select('b.codigo_bimestre', 'c.codigo_curso', 'a.codigo_alumno', 'a.nombre1_alumno', 'a.nombre2_alumno', 'a.apellido1_alumno', 'a.apellido2_alumno', 'nb.nota_examenescrito', 'nb.nota_examenoral', 'nb.nota_practica', 'nb.nota_exposicion', 'nb.nota_tarea', 'nb.promedio_final')
                    ->leftJoin('notasbimestres AS nb', function ($join) use ($codbimestre, $codcurso) {
                        $join->on('a.codigo_alumno', '=', 'nb.codigo_alumno')
                            ->where('nb.codigo_bimestre', $codbimestre)
                            ->where('nb.codigo_curso', $codcurso);
                    })
                    ->leftJoin('bimestres AS b', 'b.codigo_bimestre', '=', 'nb.codigo_bimestre')
                    ->leftJoin('cursos AS c', 'c.codigo_curso', '=', 'nb.codigo_curso')
                    ->get();


                

                    $title = 'Registro de Notas';
        
                    return view('academico.nota', compact('datos','nomcurso', 'nomgrado', 'title', 'coddocente', 'notas','nombimestre','codbimestre','codcurso' ));

                    
                } else {
                    return redirect('/')->withErrors(['ERROR' => 'No se encontró ninguna matrícula para el docente']);
                }
            } else {
                return redirect('/')->withErrors(['ERROR' => 'No se encontró el docente']);
            }

        } else {
            return redirect('/')->withErrors(['ERROR' => 'Debes logearte primero']);
        }
        

    }

    public function registro(Request $request)
{
    if (session('logged')) {
        $codigoBimestre = $request->input('codigo_bimestre');
        $codigoCurso = $request->input('codigo_curso');
        $codigoAlumno = $request->input('codigo_alumno');
        $notaExamenEscrito = $request->input('nota_examenescrito');
        $notaExamenOral = $request->input('nota_examenoral');
        $notaExposicion = $request->input('nota_exposicion');
        $notaPractica = $request->input('nota_practica');
        $notaTarea = $request->input('nota_tarea');

        $suma = $notaExamenEscrito + $notaExamenOral + $notaExposicion + $notaPractica + $notaTarea;
        $promedioFinal = $suma / 5;
        
        // Buscar un registro existente con el mismo código de bimestre y código de alumno
        $nota = NotaBimestre::updateOrCreate(
            ['codigo_bimestre' => $codigoBimestre,'codigo_curso' => $codigoCurso, 'codigo_alumno' => $codigoAlumno],
            [
                'nota_examenescrito' => $notaExamenEscrito,
                'nota_examenoral' => $notaExamenOral,
                'nota_exposicion' => $notaExposicion,
                'nota_practica' => $notaPractica,
                'nota_tarea' => $notaTarea,
                'promedio_final' => $promedioFinal
            ]
        );
        
        // Después de guardar los datos, puedes redirigir nuevamente a la página actual
        return redirect()->back();
    } else {
        return redirect('/')->withErrors(['ERROR' => 'Debes logearte primero']);
    }
}

    
}

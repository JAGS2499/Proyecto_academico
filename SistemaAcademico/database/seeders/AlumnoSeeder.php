<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AlumnoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('alumnos')->insert(
            [
                [
                    'nombre1_alumno'=>'Rosa',
                    'nombre2_alumno'=>'Maria',
                    'apellido1_alumno'=>'Perez',
                    'apellido2_alumno'=>'Lopez',
                    'codigo_matricula'=>'1'
                ],
                [
                    'nombre1_alumno'=>'Jose',
                    'nombre2_alumno'=>'Luis',
                    'apellido1_alumno'=>'Giraldo',
                    'apellido2_alumno'=>'Sanchez',
                    'codigo_matricula'=>'1'
                ]
            ]
        );
    }
}

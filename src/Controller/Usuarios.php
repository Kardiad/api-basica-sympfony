<?php

namespace App\Controller;

use App\Entity\Usuario;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class Usuarios extends AbstractController{

    #[Route("/usuario/nuevo", methods:["POST"])]
    public function nuevoUsuario(EntityManagerInterface $manager, Request $request ):Response{
        $input = $request->request->all();
        $usuario = new Usuario();
        $status = 500;
        $message = "Error algo fue mal";
        if(!empty($input) && $input['usuario'] && $input['mail'] && $input['contrasena']){
            $usuario->setUsuario($input['usuario']);
            $usuario->setMail($input['mail']);
            $usuario->setContrasena(password_hash($input['contrasena'], PASSWORD_BCRYPT));
            $manager->persist($usuario);
            $manager->flush();
            $status = 200;
            $message = "Usuario insertado a bbdd";
        }
        header("Content-Type:application/json");
        return new Response(json_encode([
            "status" => $status,
            "msg" => $message
        ]));
    }

    #[Route("/usuario/login", methods:["POST"])]
    public function obtenerUsuario(EntityManagerInterface $manager, Request $request):Response{
        $input = $request->request->all();
        $usuario = null;
        $response = [
            "status" => 400,
            "msg" => "Credenciales erroneas",
            "data" => []
        ];
        if(!empty($input) && $input['usuario']!=""){
            $usuario =  ($manager->getRepository(Usuario::class))->findOneBy([
                "usuario" => $input['usuario']
            ]);
        }
        if($input['contrasena']!="" &&
            password_verify($input['contrasena'], $usuario->getContrasena())){
                $response = [
                    "status" => 200,
                    "msg" => "Credenciales correctas",
                    "data" => [
                        "id" => $usuario->getId(),
                        "usuario" => $usuario->getUsuario(),
                        "mail" => $usuario->getMail()
                    ]
                ];
        }
        header("Content-Type:application/json");
        return new Response(json_encode($response));
    }

    #[Route("/usuario/actualizar", methods:["POST"])]
    public function actualizarUsuario(EntityManagerInterface $manager, Request $request):Response{
        $input = $request->request->all();
        $usuario = null;
        $status = 500;
        $msg = "Error algo fue mal";
        if(!empty($input) && $input['usuario']!="" || $input["contrasena"]!="" || $input["mail"]!=""){
            $meta = $manager->getRepository(Usuario::class);
            $usuario = $meta->findOneBy([
                'usuario' => $input['usuario']
            ]);
            $usuario->setContrasena(password_hash($input["contrasena"], PASSWORD_BCRYPT));
            $usuario->setMail($input["mail"]);
            $status = 200;
            $msg = "Datos actualizados";
            $manager->flush();
        }
        header("Content-Type:application/json");
        return new Response(json_encode([
            "status" => $status,
            "msg" => $msg
        ]));
    }

    #[Route("/usuario/borrar", methods:["POST"])]
    public function borrarUsuario(EntityManagerInterface $manager, Request $request):Response{
        $input = $request->request->all();
        $usuario = null;
        $response = [
            "status" => 400,
            "msg" => "Credenciales erroneas",
            "data" => []
        ];
        if(!empty($input) && $input['usuario']!=""){
            $usuario =  $manager->getRepository(Usuario::class)->findOneBy([
                "usuario" => $input['usuario']
            ]);
        }
        if($input['contrasena']!="" &&
            password_verify($input['contrasena'], $usuario->getContrasena())){
                $response['data'] = $usuario;
                $response['status'] = 200;
                $manager->remove($usuario);
                $manager->flush();
                $response = [
                    "status" => 200,
                    "msg" => "Usuario borrado con exito, ¡hasta otra!",
                    "data" => []
                ];
        }
        header("Content-Type:application/json");
        return new Response(json_encode($response));
    }

    //Falta implementar oauth

}

?>
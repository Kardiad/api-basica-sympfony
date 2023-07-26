<?php

namespace App\Controller;

use App\Entity\Heroes as EntityHeroes;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class Heroes extends AbstractController {

    #[Route("/heroe/nuevo", methods:["POST"])]
    public function crearHeroe(EntityManagerInterface $entityManager, Request $request): Response{
        $model = new EntityHeroes();
        $status = 500;
        $msg = "/nuevo fails!!!";
        $input = $request->request->all();
        if(!empty($input)){
            $status = 200;
            $msg = "/nuevo works!!!";
            $model->setNombre($input["nombre"]);
            $model->setAlterego($input["alterego"]);
            $model->setCodigo($input["codigo"]);
            $model->setAparicion($input["aparicion"]);
            $entityManager->persist($model);
            $entityManager->flush();
            header("Content-Type: application/json");
        }
        return new Response(json_encode([
            "status" => $status,
            "msg" => $msg
        ]));
    }

    #[Route("/heroe/lista", methods:["GET"])]
    public function mostrarHeroes(EntityManagerInterface $entityManager): Response{
        $manager = $entityManager->getRepository(EntityHeroes::class);
        $data = $manager->findAll();
        header("Content-Type: application/json");
        return new Response(json_encode(array_map(fn($e)=>[
            "id" => $e->getId(),
            "nombre" => $e->getNombre(),
            "codigo" => $e->getCodigo(),
            "aparicion" => $e->getAparicion(),
            "alterego" => $e->getAlterego()
        ], $data)));
    }

    #[Route("/heroe/modificar/{id}", methods:["PATCH"])]
    public function modificarHeroes(int $id, EntityManagerInterface $entityManager, Request $request): Response{
        $id ?? 0;
        $input = $request->query->all();
        $status = 500;
        if(!empty($input) && $id > 0){
            $status = 200;
            //Hacer el update
            $hero = $entityManager->find(EntityHeroes::class, $id);
            !empty($input['nombre'])? $hero->setNombre($input["nombre"]) : "";
            !empty($input['alterego'])? $hero->setAlterego($input["alterego"]) : "";
            !empty($input['codigo'])? $hero->setCodigo($input["codigo"]) : "";
            !empty($input['aparicion'])? $hero->setAparicion($input["aparicion"]) : "";
            $entityManager->flush();
        }
        header("Content-Type:application/json");
        return new Response(json_encode([
            "status" => $status,
            "id" => $id,
            "request" => $input
        ]));
    }

    #[Route("/heroe/borrar/{id}", methods:["DELETE"])]
    public function borrarHeroes(int $id, EntityManagerInterface $entityManager, Request $request): Response{
        $id ?? 0;
        $status = 500;
        if($id>0){
            $status = 200;
            $hero = $entityManager->find(EntityHeroes::class, $id);
            $entityManager->remove($hero);
            $entityManager->flush();
        }
        header("Content-Type:application/json");
        return new Response(json_encode([
            "status" => $status,
            "id" => $id
        ]));
    }

}

?>
<?php

namespace App\Controller;

use App\Entity\Heroes as EntityHeroes;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class Heroes extends AbstractController {

    public function __construct() {
        header("Content-Type: application/json");
        header("Access-Control-Allow-Origin: *");
    }

    #[Route("/heroe/nuevo", methods:["POST"])]
    public function crearHeroe(EntityManagerInterface $entityManager, Request $request): Response{
        $model = new EntityHeroes();
        $status = 500;
        $msg = "/nuevo fails!!!";
        $input = $request->request->all();
        $mime = $request->files->get("img")->getMimeType();
        $size = filesize($request->files->get("img")->getPathName());
        $img = "data:$mime;base64,".base64_encode(file_get_contents($request->files->get("img")->getPathName()));
        if(!empty($input)){
            $status = 200;
            $msg = "/nuevo works!!!";
            $input["img"] = $img;
            $input["size"] = $size;
            $model->setNombre($input["nombre"]);
            $model->setAlterego($input["alterego"]);
            $model->setCodigo($input["codigo"]);
            $model->setAparicion($input["aparicion"]);
            $model->setImg($img);
            $model->setImgSize($size);
            $entityManager->persist($model);
            $entityManager->flush();
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
        return new Response(json_encode(array_map(fn($e)=>[
            "id" => $e->getId(),
            "nombre" => $e->getNombre(),
            "codigo" => $e->getCodigo(),
            "aparicion" => $e->getAparicion(),
            "alterego" => $e->getAlterego(),
            "img" => fread($e->getImg(), $e->getImgSize())
        ], $data)));
    }

    #[Route("/heroe/obtener/{id}")]
    public function obtenerHeroe(int $id, EntityManagerInterface $entityManager, Request $request): Response{
        $id ?? 0 ;
        $hero = null;
        $send = null;
        if($id>0){
            $hero = $entityManager->find(EntityHeroes::class, $id);
        }
        if($hero != null){
            $send = json_encode([    
                0 => [
                    "id" => $hero->getId(),
                    "nombre" => $hero->getNombre(),
                    "codigo" => $hero->getCodigo(),
                    "aparicion" => $hero->getAparicion(),
                    "alterego" => $hero->getAlterego(),
                    "img" => fread($hero->getImg(), $hero->getImgSize())
                ]    
            ]);
        }
        return new Response($send);

    }

    #[Route("/heroe/modificar/{id}", methods:["PATCH"])]
    public function modificarHeroes(int $id, EntityManagerInterface $entityManager, Request $request): Response{
        $id ?? 0;
        $input = $request->query->all();
        $hero = null;
        if(!empty($input) && $id > 0){
            $status = 400;
            $hero = $entityManager->find(EntityHeroes::class, $id);
        }
        if($hero){
            $status = 200;
            !empty($input['nombre'])? $hero->setNombre($input["nombre"]) : "";
            !empty($input['alterego'])? $hero->setAlterego($input["alterego"]) : "";
            !empty($input['codigo'])? $hero->setCodigo($input["codigo"]) : "";
            !empty($input['aparicion'])? $hero->setAparicion($input["aparicion"]) : "";
            $entityManager->flush();
        }
        return new Response(json_encode([
            "status" => $status,
            "id" => $id
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
        return new Response(json_encode([
            "status" => $status,
            "id" => $id
        ]));
    }

    #[Route("/heroe/buscar", methods:["GET"])]
    public function buscarHeroePorParam(EntityManagerInterface $manager, Request $request) : Response {
        $param = $request->query->get('q');
        $heroes = [];
        if($param){
           $heroes =array_map(fn($e)=>[
            "id" => $e->getId(),
            "nombre" => $e->getNombre(),
            "codigo" => $e->getCodigo(),
            "aparicion" => $e->getAparicion(),
            "alterego" => $e->getAlterego(),
            "img" => fread($e->getImg(), $e->getImgSize())
           ], $manager->getRepository(EntityHeroes::class)
                ->findByParams($param));
           
        }
        return new Response(json_encode($heroes));
    }

    //Paginación para otro momento

}

?>
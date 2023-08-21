<?php

namespace App\Controller;

use App\Entity\Heroes as EntityHeroes;
use App\Helpers\Patchgetter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class Heroes extends AbstractController {

    private string $base_url = "";
    private string $hero_path = "";
    private string $public_path = "";

    public function __construct() {
       $this->base_url = $_SERVER["SYMFONY_APPLICATION_DEFAULT_ROUTE_SCHEME"]."://".$_SERVER['HTTP_HOST'];
       $this->hero_path = '/uploads/heroes/';
       $this->public_path = $_SERVER['DOCUMENT_ROOT'];
       
    }

    #[Route("/heroe/nuevo", methods:["POST"])]
    public function crearHeroe(EntityManagerInterface $entityManager, Request $request): Response{
        //TODO testing some type of files
        $model = new EntityHeroes();
        $input = $request->request->all();
        $img = $request->files->get("img");
        $new_name = md5($img->getClientOriginalName()).'.'.$img->guessExtension();
        $path = $this->base_url.$this->hero_path.$new_name;
        $img->move($this->getParameter("uploads_hero"), $new_name);
        $codigo = explode(" ", $input['editorial'])[0]."-".$input['nombre'];
        $input['codigo'] = $codigo;
        $status = 500;
        $msg = "error";
        if(!empty($input)){
            $status = "200";
            $msg ="works";
            $model->setNombre($input["nombre"]);
            $model->setAlterego($input["alterego"]);
            $model->setCodigo($codigo);
            $model->setAparicion($input["aparicion"]);
            $model->setImg($path);
            $model->setEditorial($input['editorial']);
            $model->setCreador($input['creador']);
            $entityManager->persist($model);
            $entityManager->flush();
        }

        return new Response(json_encode([
            'status' => $status,
            'msg' => $msg,
            'data' =>
                [0 => $model->getArray()]
        ]));
    }

    #[Route("/heroe/lista", methods:["GET"])]
    public function mostrarHeroes(EntityManagerInterface $entityManager): Response{
        $manager = $entityManager->getRepository(EntityHeroes::class);
        $data = $manager->findAll();
        return new Response(json_encode([
            'status' => 200,
            'msg' => "works",
            'data' => array_map(fn($e)=>[
                "id" => $e->getId(),
                "nombre" => $e->getNombre(),
                "codigo" => $e->getCodigo(),
                "aparicion" => $e->getAparicion(),
                "alterego" => $e->getAlterego(),
                "img" => $e->getImg(),
                "editorial" => $e->getEditorial(),
                "creador" => $e->getCreador()
        ], $data)
        ]));
    }

    #[Route("/heroe/obtener/{id}")]
    public function obtenerHeroe(int $id, EntityManagerInterface $entityManager, Request $request): Response{
        $id ?? 0 ;
        $hero = null;
        $send = null;
        $status = 500;
        $msg = "not found";
        if($id>0){
            $status = 200;
            $hero = $entityManager->find(EntityHeroes::class, $id);
        }
        if($hero != null){
            $send = json_encode([
                'status' => $status,     
                'msg' => $msg,
                'data' =>[
                    0 => [
                        "id" => $hero->getId(),
                        "nombre" => $hero->getNombre(),
                        "codigo" => $hero->getCodigo(),
                        "aparicion" => $hero->getAparicion(),
                        "alterego" => $hero->getAlterego(),
                        "editorial" => $hero->getEditorial(),
                        "img" => $hero->getImg(),
                        'creador' => $hero->getCreador()
                    ]    
                ]
            ]);
        }
        return new Response($send);

    }

    #[Route("/heroe/modificar/{id}", methods:["PATCH"])]
    public function modificarHeroes(int $id, EntityManagerInterface $entityManager, Request $request): Response{
        //header('Access-Control-Allow-Origin: *');
        $id ?? 0;
        $input = $request->query->all();
        $hero = null;
        $input = (new Patchgetter($this->public_path.'/'.$this->hero_path))->get();
        $status = 500;
        $path = $this->base_url.$this->hero_path.'/'.$input['img']['filename'];
        $msg = "Server error";
        if(!empty($input) && $id > 0){
            $hero = $entityManager->find(EntityHeroes::class, $id);
        }
        if(!$hero){
            $status = 400;
            $msg = "Hero not found";
        }
        if($hero){
            $status = 200;
            $msg = "Works propperly";
            !empty($input['nombre'])? $hero->setNombre($input["nombre"]) : "";
            !empty($input['alterego'])? $hero->setAlterego($input["alterego"]) : "";
            !empty($input['codigo'])? $hero->setCodigo($input["codigo"]) : "";
            !empty($input['aparicion'])? $hero->setAparicion($input["aparicion"]) : "";
            !empty($input['img'])?$hero->setImg($path):"";
            !empty($input['editorial'])?$hero->setEditorial($input['editorial']):"";
            !empty($input['creador'])?$hero->setCreador($input['creador']):"";
            $entityManager->flush();
        }
        return new Response(json_encode([
            "status" => $status,
            "msg" => $msg,
            "data" => [
                0 => [
                    "nombre" => $hero->getNombre(),
                    "alterego" => $hero->getAlterego(),
                    "codigo" => $hero->getCodigo(),
                    "aparicion" => $hero->getAparicion(),
                    "img" => $hero->getImg(),
                    "editorial" => $hero->getEditorial(),
                    "creador" => $hero->getCreador()
                ]
            ]
        ]));
    }

    #[Route("/heroe/borrar/{id}", methods:["DELETE"])]
    public function borrarHeroes(int $id, EntityManagerInterface $entityManager, Request $request): Response{
        $id ?? 0;
        $status = 500;
        $hero = null;
        $file_path = null;
        //TODO borrar los archivos de imagen, dado que sin darme cuenta es única
        if($id !=0){
            $hero = $entityManager->find(EntityHeroes::class, $id);
            $file_path = $this->public_path.'/'.$this->hero_path.'/'.explode('/', $hero->getImg())[count(explode('/', $hero->getImg()))-1];
        }
        if(file_exists($file_path)){
            unlink($this->public_path.'/'.$this->hero_path.'/'.explode('/', $hero->getImg())[count(explode('/', $hero->getImg()))-1]);
        }
        if($hero != null){
            $status = 200;
            $entityManager->remove($hero);
            $entityManager->flush();
        }
        return new Response(json_encode([
            "status" => $status,
            "msg" => "Heroe $id deleted succesfull"
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
            "img" => $e->getImg(), $e->getImgSize()
           ], $manager->getRepository(EntityHeroes::class)
                ->findByParams($param));
           
        }
        return new Response(json_encode([
            "status" => 200,
            "msg" => "Search completed",
            "data" => $heroes
        ]));
    }
    //Paginación para otro momento

}

?>
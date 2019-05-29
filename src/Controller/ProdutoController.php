<?php

namespace App\Controller;

use App\Entity\Produto;
use App\Form\ProdutoType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;


class ProdutoController extends AbstractController
{

    /**
     * @Route("/produto", name="listar-produto")
     */
    public function index()
    {
       $em = $this->getDoctrine()->getManager();
       $produtos = $em->getRepository(Produto::class)->findAll();
       return $this->render("produto/index.html.twig", [
           'produtos' => $produtos
       ]);
    }


    /**
     * @param Request $request
     *
     * @Route("/produto/cadastrar", name="cadastrar-produto")
     */
    public function create(Request $request)
    {
        $produto = new Produto();

        $form = $this->createForm(ProdutoType::class, $produto);
        $form->handleRequest($request);
        $mensagem = '';


        if($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($produto);
            $em->flush();

            $session = new Session();
            $session->getFlashBag()->add('success', 'Produto salvo com sucesso!');

//            $this->get('session')->getFlashBag()->set('success','Produto foi salvo com sucesso!');

            return $this->redirectToRoute('listar-produto');
        }

        return $this->render('produto/create.html.twig', [
            'form' => $form->createView()
        ]);
    }


    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @ROUTE("produto/editar/{id}", name="editar-produto")
     */
    public function update(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $produto = $em->getRepository(Produto::class)->find($id);

        $form = $this->createForm(ProdutoType::class, $produto);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $em->persist($produto);
            $em->flush();
            $session = (new Session)->getFlashBag()->add('success', 'O Produto '. $produto->getNome().' foi alterado com sucesso!');
            return $this->redirectToRoute('listar-produto');
        }

        return $this->render('produto/update.html.twig', [
            'produto' => $produto,
            'form' => $form->createView()
        ]);
    }


    /**
     * @param Request $request
     * @param $id
     *
     * @Route("produto/visualizar/{id}", name="visualizar-produto")
     */
    public function view(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $produto = $em->getRepository(Produto::class)->find($id);

        return $this->render('produto/view.html.twig', [
            'produto' => $produto
        ]);
    }

    /**
     * @param Request $request
     * @param $id
     *
     * @Route("produto/apagar/{id}", name="apagar-produto")
     */
    public function delete(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $produto = $em->getRepository(Produto::class)->find($id);

        if(!$produto) {
            $mensagem = "Produto não foi encontrado";
            $tipo = "warning";
        } else {
            $em->remove($produto);
            $em->flush();
            $mensagem = "O produto " . $produto->getNome() . " foi excluído com sucesso";
            $tipo = "success";
        }

        $session = (new Session)->getFlashBag()->add($tipo, $mensagem);
        return $this->redirectToRoute('listar-produto');
    }

}

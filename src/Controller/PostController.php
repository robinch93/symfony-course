<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/post", name="post.")
 */
class PostController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(PostRepository $postRepository)
    {
        $posts = $postRepository->findAll();

        return $this->render('post/index.html.twig', [
            'posts' => $posts
        ]);
    }

    /**
     * @Route("/create", name="create")
     * @param Request $request
     * @param FileUploader $fileUploader
     * @return Response
     */
    public function create(Request $request)
    {
        // create new post
        $post = new Post();

        $form = $this->createForm(PostType::class,$post);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $em = $this->getDoctrine()->getManager();
            /** @var UploadedFile $file  */
            $file = $request->files->get('post')['attachment'];
            if($file){
                $filename = md5(uniqid("",true)) .  '.' . $file->getClientOriginalExtension();

                $file->move(
                    $this->getParameter('uploads_dir'),
                    $filename
                );

                $post->setImage($filename);

                $em->persist($post);
                $em->flush();
            }

            return $this->redirect($this->generateUrl('post.index'));

        }

        return $this->render('post/create.html.twig',[
            'form' => $form->createView()
        ]);

    }

    /**
     * @Route("/show/{id}", name="show")
     * @param Post $post
     * @return Response
     */
    public function show($id, PostRepository $postRepository)
    {
        $post = $postRepository->find($id);

        return $this->render('post/show.html.twig',[
            'post' => $post
        ]);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function remove($id, PostRepository $postRepository)
    {
        $post = $postRepository->find($id);

        $em = $this->getDoctrine()->getManager();
        $em->remove($post);
        $em->flush();

        $this->addFlash('success','Post was removed');

        return $this->redirect($this->generateUrl('post.index'));
    }
}

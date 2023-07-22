<?php

namespace App\Controller;

use App\Entity\Address;
use App\Entity\Category;
use App\Form\AddressType;
use App\Manager\CartManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class AddressController extends AbstractController
{
    private $entityManager;
    private $security;

    public function __construct(EntityManagerInterface $entityManager, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    /**
     * @Route("/address/create", name="address_create")
     * @IsGranted("ROLE_USER")
     */
    public function create(Request $request, CartManager $cartManager): Response
    {
        $address = new Address();
        $address->setUser($this->security->getUser()); // Đặt user cho đối tượng Address
        $categories = $this->entityManager->getRepository(Category::class)->findAll();
        $cart = $cartManager->getCurrentCart();
        $form = $this->createForm(AddressType::class, $address);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Lưu địa chỉ vào cơ sở dữ liệu
            $this->entityManager->persist($address);
            $this->entityManager->flush();

            // Chuyển hướng sau khi lưu thành công
            return $this->redirectToRoute('address_list');
        }

        return $this->render('address/create.html.twig', [
            'form' => $form->createView(),
            'categories' => $categories,
            'cart' => $cart,
        ]);
    }

    /**
     * @Route("/address/list", name="address_list")
     * @IsGranted("ROLE_SUPER_ADMIN", message="Chỉ có Admin mới có thể xem trang này")
     */
    public function list(ManagerRegistry $doctrine, CartManager $cartManager, SessionInterface $session): Response
    {
        // Lấy danh sách địa chỉ từ cơ sở dữ liệu
        $addresses = $this->entityManager->getRepository(Address::class)->findAll();
        $categories = $doctrine->getRepository(Category::class)->findAll();
        $cart = $cartManager->getCurrentCart();

        return $this->render('address/list.html.twig', [
            'addresses' => $addresses,
            'categories' => $categories,
            'cart' => $cart,
        ]);
    }

    /**
     * @Route("/address/success", name="address_success")
     * @IsGranted("ROLE_USER")
     */
    public function success(Request $request, CartManager $cartManager, ManagerRegistry $doctrine): Response
    {
        $addresses = $this->entityManager->getRepository(Address::class)->findAll();
        $categories = $doctrine->getRepository(Category::class)->findAll();
        $cart = $cartManager->getCurrentCart();

        return $this->render('address/success.html.twig', [
            'addresses' => $addresses,
            'categories' => $categories,
            'cart' => $cart,
        ]);
    }
}


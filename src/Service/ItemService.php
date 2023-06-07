<?php

namespace App\Service;

use App\Entity\Item;
use App\Entity\Transaction;
use App\Entity\User;
use App\Exceptions\NotEnoughMoney;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Translation\Exception\NotFoundResourceException;


class ItemService extends CategoryService
{
    private Security $security;
    private Filesystem $filesystem;


    public function __construct(EntityManagerInterface $entityManager, Security $security, Filesystem $filesystem)
    {
        parent::__construct($entityManager);
        $this->security = $security;
        $this->filesystem = $filesystem;
    }

    public function clearFileName(mixed $filename): string
    {
        /** @var UploadedFile $filename */
        $originalFileName = pathinfo($filename->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFileName = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_]
         remove; Lower()', $originalFileName);

        return $safeFileName . '-' . uniqid() . '.' . $filename->guessExtension();
    }

    public function getData(FormInterface $form): array
    {
        return [
            'name' => $form->get('name')->getData(),
            'title' => $form->get('title')->getData(),
            'price' => $form->get('price')->getData(),
            'image' => $form->get('image')->getData(),
            'category' => $form->get('category')->getData(),
            'owner' => $this->security->getUser(),
            'uploadedAt' => new DateTime()
        ];
    }

    public function addItem(array $data): void
    {
        $newFileName = $this->clearFileName($data['image']);
        $item = $this->createNewItem($data, $newFileName);
        $category = $this->createNewCategory($item, $data);
        $this->moveImage($data['image'], $newFileName);
        $this->entityManager->persist($item);
        $this->entityManager->persist($category);
        $this->entityManager->flush();
    }

    public function getUnsoldItems(): array
    {
        return $this->entityManager->getRepository(Item::class)->findBy(['sold' => 0], ['uploaded_at' => 'DESC']);
    }

    public function searchItems(array $params,$phrase): array
    {
        $items = $this->entityManager->getRepository(Item::class)->search($params,$phrase);

        if (!$items) {
            throw new NotFoundResourceException();
        }

        return $items;
    }


    public function findProductById(int $id): Item
    {
        $item = $this->entityManager->getRepository(Item::class)->findOneBy(['id' => $id, 'sold' => 0]);

        if (!$item) {
            throw new NotFoundResourceException();
        }

        return $item;
    }

    public function checkItemOwner(Item $item): bool
    {
        if ($this->security->getUser() === $item->getOwner()) {
            return true;
        } else {
            throw new UnsupportedUserException();
        }
    }

    public function getUserItems(UserInterface $user): array
    {
        return $this->entityManager->getRepository(Item::class)->findBy(['owner' => $user, 'sold' => 0]);
    }

    public function editItem(Item $item, array $newData): void
    {
        if ($this->checkItemOwner($item)) {
            $item->setName($newData['name']);
            $item->setTitle($newData['title']);
            $item->setPrice($newData['price']);
            $oldImage = $item->getImage();
            $category = $this->createNewCategory($item, $newData);
            if ($newData['image'] !== null) {
                $newImage = $this->clearFileName($newData['image']);
                $item->setImage($newImage);
                $this->moveImage($newData['image'], $newImage);
                $this->removeImage($oldImage);
            }
            $this->entityManager->persist($category);
            $this->entityManager->persist($item);
            $this->entityManager->flush();


        } else {
            throw new UnsupportedUserException();
        }
    }


    public function removeItem(int $id): void
    {
        $item = $this->findProductById($id);

        if ($this->checkItemOwner($item)) {
            $this->removeImage($item->getImage());
            $this->entityManager->remove($item);
            $this->entityManager->flush();
        } else {
            throw new UnsupportedUserException();
        }
    }

    /**
     * @throws NotEnoughMoney
     */
    public function buyItem(int $id): void
    {
        $item = $this->findProductById($id);
        $user = $this->security->getUser();
        $userBalance = 0;
        if ($user instanceof User) {
            $userBalance = $user->getBalance();
        }

        if ($userBalance < $item->getPrice()) {
            throw new NotEnoughMoney();
        } elseif ($user === $item->getOwner()) {
            throw new UnsupportedUserException();
        } else {
            $transaction = $this->createNewTransaction($item);
            $this->makeTransaction($transaction, $item);
            $user->subBalance($item->getPrice());
            $this->updateUser($user);
            $this->removeImage($item->getImage());
        }
    }

    private function createNewTransaction(Item $item): Transaction
    {
        $transaction = new Transaction();
        $transaction->setBuyer($this->security->getUser());
        $transaction->setSeller($item->getOwner());
        $transaction->setDate(new DateTime());
        $transaction->setItem($item);

        return $transaction;
    }

    private function makeTransaction(Transaction $transaction, Item $item): void
    {
        $item->setSold(1);
        $this->entityManager->persist($item);
        $this->entityManager->persist($transaction);
        $this->entityManager->flush();
    }

    private function removeImage(string $filename): void
    {
        $this->filesystem->remove('images/products/' . $filename);
    }

    /** @var UploadedFile $originalFileName */
    private function moveImage(mixed $originalFileName, string $newFileName): void
    {
        $originalFileName->move('images/products', $newFileName);
    }

    private function updateUser(User $user):void
    {
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    private function createNewItem(array $data, mixed $filename): Item
    {
        $item = new Item();
        $item->setOwner($this->security->getUser());
        $item->setName($data['name']);
        $item->setTitle($data['title']);
        $item->setPrice($data['price']);
        $item->setImage($filename);
        $item->setUploadedAt($data['uploadedAt']);
        $item->setSold(0);


        return $item;
    }
}

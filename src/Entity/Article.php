<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ArticleRepository;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
#[Vich\Uploadable]
class Article
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'articles')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $author = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\Column]
    private ?int $verifiedAdmin = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $situationTravail = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $domaine = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $fileName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $fileSize = null;

    #[Vich\UploadableField(mapping: 'articles', fileNameProperty: 'fileName', size: 'fileSize')]
    private ?File $projectFile = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): static
    {
        $this->author = $author;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getVerifiedAdmin(): ?int
    {
        return $this->verifiedAdmin;
    }

    public function setVerifiedAdmin(int $verifiedAdmin): static
    {
        $this->verifiedAdmin = $verifiedAdmin;

        return $this;
    }

    public function getSituationTravail(): ?string
    {
        return $this->situationTravail;
    }

    public function setSituationTravail(?string $situationTravail): static
    {
        $this->situationTravail = $situationTravail;

        return $this;
    }

    public function getDomaine(): ?string
    {
        return $this->domaine;
    }

    public function setDomaine(?string $domaine): static
    {
        $this->domaine = $domaine;

        return $this;
    }

    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    public function setFileName(?string $fileName): static
    {
        $this->fileName = $fileName;

        return $this;
    }

    public function getFileSize(): ?string
    {
        return $this->fileSize;
    }

    public function setFileSize(?string $fileSize): static
    {
        $this->fileSize = $fileSize;

        return $this;
    }

    public function getProjectFile()
    {
        return $this->projectFile;
    }

    public function setProjectFile(?File $projectFile = null): void
    {
        $this->projectFile = $projectFile;

        if (null !== $projectFile) {
            $this->updatedAt = new \DateTime();
        }
    }
}

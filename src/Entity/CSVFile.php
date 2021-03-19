<?php

namespace App\Entity;

use App\Repository\CSVFileRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CSVFileRepository::class)
 */
class CSVFile
{

    /**
     *
     * @Assert\NotBlank(message="Veuillez joindre un fichier Excel CSV !!!.")
     * @Assert\File(
     *        mimeTypesMessage = "Veuillez joindre un fichier Excel CSV !!!.",
     *        maxSize = "4M",
     *        maxSizeMessage = "Le fichier à joindre est trop volumineux !!!."
     * )
     */
    private $file;

    public function getFile()
    {
        return $this->file;
    }

    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }
}

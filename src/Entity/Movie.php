<?php

namespace App\Entity;

use App\Model\Rated;
use App\Repository\MovieRepository;
use App\Validator\Constraints\MovieSlug;
use App\Validator\Constraints\Poster;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Validator\Constraints\NotNull;

#[ORM\Entity(repositoryClass: MovieRepository::class)]
#[UniqueConstraint(name: 'movie_slug', columns: ['slug'])]
class Movie
{
    public const SLUG_PATTERN = '\d{4}-\w+(-\w+)*';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[NotNull]
    #[Length(min: 3, max: 255)]
    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[NotNull]
    #[MovieSlug]
    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[NotNull]
    #[Length(min: 20)]
    #[ORM\Column(type: Types::TEXT)]
    private ?string $plot = null;

    #[NotNull]
    #[Length(min: 5)]
    #[Poster()]
    #[ORM\Column(length: 255)]
    private ?string $poster = null;

    #[NotNull]
    #[LessThanOrEqual('today')]
    #[ORM\Column(type: Types::DATETIMETZ_IMMUTABLE)]
    private ?\DateTimeImmutable $releasedAt = null;

    #[NotNull]
    #[Count(min: 1)]
    #[ORM\ManyToMany(targetEntity: Genre::class, inversedBy: 'movies')]
    private Collection $genres;

    #[NotNull]
    #[ORM\Column(length: 8, enumType: Rated::class, options: ['default' => Rated::GeneralAudiences])]
    private ?Rated $rated = Rated::GeneralAudiences;

    public function __construct()
    {
        $this->genres = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getPlot(): ?string
    {
        return $this->plot;
    }

    public function setPlot(string $plot): self
    {
        $this->plot = $plot;

        return $this;
    }

    public function getPoster(): ?string
    {
        return $this->poster;
    }

    public function setPoster(string $poster): self
    {
        $this->poster = $poster;

        return $this;
    }

    public function getReleasedAt(): ?\DateTimeImmutable
    {
        return $this->releasedAt;
    }

    public function setReleasedAt(\DateTimeImmutable $releasedAt): self
    {
        $this->releasedAt = $releasedAt;

        return $this;
    }

    /**
     * @return Collection<int, Genre>
     */
    public function getGenres(): Collection
    {
        return $this->genres;
    }

    public function addGenre(Genre $genre): self
    {
        if (!$this->genres->contains($genre)) {
            $this->genres->add($genre);
        }

        return $this;
    }

    public function removeGenre(Genre $genre): self
    {
        $this->genres->removeElement($genre);

        return $this;
    }

    public function getRated(): ?Rated
    {
        return $this->rated;
    }

    public function setRated(Rated $rated): self
    {
        $this->rated = $rated;

        return $this;
    }
}

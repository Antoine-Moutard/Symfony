<?php

namespace App\Entity;

use App\Repository\SongRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SongRepository::class)]
class Song
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $artist = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(length: 255)]
    private ?string $duration = null;

    #[ORM\ManyToMany(targetEntity:User::class, mappedBy:"likedSongs")]
    private $users;


    #[ORM\ManyToOne(targetEntity:Genre::class)]
    private $genre;


    public function __construct()
    {
        $this->likedSongs = new ArrayCollection();
        $this->likedByUsers = new ArrayCollection();
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getArtist(): ?string
    {
        return $this->artist;
    }

    public function setArtist(string $artist): self
    {
        $this->artist = $artist;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return Collection<int, LikedSongs>
     */
    public function getLikedSongs(): Collection
    {
        return $this->likedSongs;
    }

    public function addLikedSong(LikedSongs $likedSong): self
    {
        if (!$this->likedSongs->contains($likedSong)) {
            $this->likedSongs->add($likedSong);
            $likedSong->setSong($this);
        }

        return $this;
    }

    public function isLiked(User $user, Song $song): bool
    {
        $likedSongs = $user->getLikedSongs();
        foreach ($likedSongs as $likedSong)
        {
            if ($likedSong->getId() === $song->getId()) 
            {
                return true;
            }
        }

        return false;
    }

    public function removeLikedSong(UserLikedSongs $likedSong): self
    {
        if ($this->likedSongs->removeElement($likedSong)) {
            // set the owning side to null (unless already changed)
            if ($likedSong->getSong() === $this) {
                $likedSong->setSong(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, LikedSong>
     */
    public function getLikedByUsers(): Collection
    {
        return $this->likedByUsers;
    }

    public function addLikedByUser(LikedSong $likedByUser): self
    {
        if (!$this->likedByUsers->contains($likedByUser)) {
            $this->likedByUsers->add($likedByUser);
            $likedByUser->setSong($this);
        }

        return $this;
    }

    public function removeLikedByUser(LikedSong $likedByUser): self
    {
        if ($this->likedByUsers->removeElement($likedByUser)) {
            // set the owning side to null (unless already changed)
            if ($likedByUser->getSong() === $this) {
                $likedByUser->setSong(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->addLikedSong($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            $user->removeLikedSong($this);
        }

        return $this;
    }

    public function getGenre(): ?Genre
    {
        return $this->genre;
    }

    public function setGenre(?Genre $genre): self
    {
        $this->genre = $genre;

        return $this;
    }

    public function getDuration(): ?string
    {
        return $this->duration;
    }

    public function setDuration(string $duration): self
    {
        $this->duration = $duration;

        return $this;
    }
}

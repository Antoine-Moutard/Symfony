<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Song;
use App\Entity\User;

class SongController extends AbstractController
{
    #[Route('/song', name: 'app_song')]
    public function index(): Response
    {
        return $this->render('song/index.html.twig', [
            'controller_name' => 'SongController',
        ]);
    }


    #[Route('/add', name: 'app_add_song')]
    public function addSongAction(ManagerRegistry $doctrine)
    {
        $song = new Song();

        $song->setName("MyName");
        $date = new \DateTime('2442-02-03');
        $song->setDate($date);
        $song->setArtist("c'est nul");
        $song->setGenre("zezezezeze");
        $entityManager = $doctrine->getManager();
        $entityManager->persist($song);
        $entityManager->flush();
        return $this->render('song/index.html.twig', ['controller_name' => 'FilmController',]);
    }

    #[Route('/displaySong/{id}', name: 'app_display_songId')]

    public function displaySongAction($id, ManagerRegistry $doctrine)
    {
        $song = $doctrine->getManager()->getRepository(Song::class)->find($id);
        
        return $this->render('song/song.html.twig', array('song' => $song));
    }

    #[Route('/', name: 'app_display_all_song')]

    public function displayAllSongAction(ManagerRegistry $doctrine)
    {
        $songs = $doctrine->getManager()->getRepository(Song::class)->findAll();
        $user = $this->getUser();

        if(!$songs)
        {
            $message ='No songs available';
        }
        else
        {
            $message = null;
        }

        if(!$user)
        {
            $mostlikedsongimg = [];
        

            $conn = new mysqli('localhost', 'root', '', 'top100');
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }
            
            $sql = "SELECT song.duration, count(user_id) from likedsong join song on likedsong.song_id = song.id GROUP By song_id ORDER BY `count(user_id)` DESC limit 3";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    array_push($mostlikedsongimg, $row["duration"]); 
                }
            } 
            else {
                echo "No records has been found";
            }
            $conn->close();


        





            return $this->render('song/songs.html.twig',array('allSongs'=>$songs, 'mostLikedSongs'=>$mostlikedsongimg, 'message'=>$message));
        }
        else
        {
            $likedSongs = $user->getLikedSongs();
            

            return $this->render('song/songs.html.twig',array('allSongs'=>$songs, 'likedSongs'=>$likedSongs, 'message'=>$message));
        }

    }


    #[Route('likeSong/{songid}', name: 'app_like_song')]

    public function addLikeSong($songid, ManagerRegistry $doctrine)
    {

        $entityManager = $doctrine->getManager();

        $song = $doctrine->getManager()->getRepository(Song::class)->find($songid);
        $user = $this->getUser();
        if (!$user) {
            throw new Exception('Connect to your account to like songs');
        }
        if (!$song) {
            throw new Exception('Song not found');
        }

        $user->addLikedSong($song);
        $entityManager->flush();

        return $this->redirectToRoute('app_display_all_song');
    }

    #[Route('unlikeSong/{songid}', name: 'app_unlike_song')]

    public function deleteLikeSong($songid, ManagerRegistry $doctrine)
    {

        $entityManager = $doctrine->getManager();

        $song = $doctrine->getManager()->getRepository(Song::class)->find($songid);
        $user = $this->getUser();
        if (!$user) {
            throw new Exception('Connect to your account to like songs');
        }
        if (!$song) {
            throw new Exception('Song not found');
        }

        $user->removeLikedSong($song);
        $entityManager->flush();

        return $this->redirectToRoute('app_display_all_song');
    }

    #[Route('/like', name: 'app_display_like')]
    public function displayLikeSong(ManagerRegistry $doctrine)
    {

        
        $user = $this->getUser();

        if (!$user) {
            $message = 'Log in first';
            $allSongs = null;
        }
        else
        {
            $allSongs = $user->getLikedSongs();
 
            if (!$allSongs) {
                $message = 'No liked songs';
            }
            else
            {

                $message = null;
            }
        }

        

        return $this->render('song/likedsongs.html.twig',array('allSongs'=>$allSongs, 'message'=>$message));

        

        return $this->redirectToRoute('app_display_all_song');
    }
}

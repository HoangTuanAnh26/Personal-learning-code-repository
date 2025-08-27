import java.io.*;
import java.util.*;
import java.awt.Desktop;
import java.net.URI;

class Video {
    String title;
    String link;
    boolean seen;

    public Video(String title, String link) {
        this.title = title;
        this.link = link;
        this.seen = false;
    }

    public void open() {
        try {
            Desktop.getDesktop().browse(new URI(link));
            this.seen = true;
        } catch (Exception e) {
            System.out.println("Error opening video.");
        }
    }
}

class Playlist {
    String name;
    String description;
    int rating;
    List<Video> videos;

    public Playlist(String name, String description, int rating, List<Video> videos) {
        this.name = name;
        this.description = description;
        this.rating = rating;
        this.videos = videos;
    }
}

public class YouTubePlaylist {
    static Scanner scanner = new Scanner(System.in);

    public static Video readVideo() {
        System.out.print("Enter title: ");
        String title = scanner.nextLine();
        System.out.print("Enter link: ");
        String link = scanner.nextLine();
        return new Video(title, link);
    }

    public static List<Video> readVideos() {
        List<Video> videos = new ArrayList<>();
        System.out.print("Enter number of videos: ");
        int totalVideo = Integer.parseInt(scanner.nextLine());
        for (int i = 0; i < totalVideo; i++) {
            System.out.println("Enter video " + (i + 1) + ":");
            videos.add(readVideo());
        }
        return videos;
    }

    public static Playlist readPlaylist() {
        System.out.print("Enter playlist name: ");
        String name = scanner.nextLine();
        System.out.print("Enter playlist description: ");
        String description = scanner.nextLine();
        System.out.print("Enter rating (1-5): ");
        int rating = Integer.parseInt(scanner.nextLine());
        List<Video> videos = readVideos();
        return new Playlist(name, description, rating, videos);
    }

    public static void printPlaylist(Playlist playlist) {
        System.out.println("\n--- Playlist Info ---");
        System.out.println("Name: " + playlist.name);
        System.out.println("Description: " + playlist.description);
        System.out.println("Rating: " + playlist.rating);
        for (int i = 0; i < playlist.videos.size(); i++) {
            System.out.println((i + 1) + ". " + playlist.videos.get(i).title);
        }
    }

    public static void playVideo(Playlist playlist) {
        printPlaylist(playlist);
        System.out.print("Select a video to play (1-" + playlist.videos.size() + "): ");
        int choice = Integer.parseInt(scanner.nextLine()) - 1;
        if (choice >= 0 && choice < playlist.videos.size()) {
            playlist.videos.get(choice).open();
        } else {
            System.out.println("Invalid selection.");
        }
    }

    public static void showMenu() {
        System.out.println("\nMain Menu:");
        System.out.println("1. Create playlist");
        System.out.println("2. Show playlist");
        System.out.println("3. Play a video");
        System.out.println("4. Exit");
    }

    public static void main(String[] args) {
        Playlist playlist = null;
        while (true) {
            showMenu();
            System.out.print("Choose an option: ");
            int choice = Integer.parseInt(scanner.nextLine());
            switch (choice) {
                case 1:
                    playlist = readPlaylist();
                    break;
                case 2:
                    if (playlist != null)
                        printPlaylist(playlist);
                    else
                        System.out.println("No playlist available.");
                    break;
                case 3:
                    if (playlist != null)
                        playVideo(playlist);
                    else
                        System.out.println("No playlist available.");
                    break;
                case 4:
                    System.out.println("Goodbye!");
                    return;
                default:
                    System.out.println("Invalid choice, try again.");
            }
        }
    }
}

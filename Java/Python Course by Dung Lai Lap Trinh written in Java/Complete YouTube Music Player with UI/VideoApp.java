import javax.swing.*;
import java.awt.*;
import java.awt.event.MouseAdapter;
import java.awt.event.MouseEvent;
import java.io.*;
import java.net.URI;
import java.util.ArrayList;
import java.util.List;

class Video {
    String title;
    String link;
    boolean seen;

    public Video(String title, String link) {
        this.title = title.trim();
        this.link = link.trim();
        this.seen = false;
    }

    public void open() {
        try {
            Desktop.getDesktop().browse(new URI(link));
            System.out.println("Open " + title);
            seen = true;
        } catch (Exception e) {
            e.printStackTrace();
        }
    }
}

class Playlist {
    String name;
    String description;
    String rating;
    List<Video> videos;

    public Playlist(String name, String description, String rating, List<Video> videos) {
        this.name = name.trim();
        this.description = description.trim();
        this.rating = rating.trim();
        this.videos = videos;
    }
}

class VideoApp extends JFrame {
    private List<Playlist> playlists;
    private JPanel playlistPanel;
    private JPanel videoPanel;

    public VideoApp() {
        setTitle("Video App");
        setSize(600, 400);
        setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
        setLayout(new BorderLayout());

        playlists = readPlaylistsFromTxt("data.txt");
        playlistPanel = new JPanel();
        videoPanel = new JPanel();
        playlistPanel.setLayout(new BoxLayout(playlistPanel, BoxLayout.Y_AXIS));
        videoPanel.setLayout(new BoxLayout(videoPanel, BoxLayout.Y_AXIS));

        loadPlaylistButtons();

        add(new JScrollPane(playlistPanel), BorderLayout.WEST);
        add(new JScrollPane(videoPanel), BorderLayout.CENTER);
    }

    private void loadPlaylistButtons() {
        playlistPanel.removeAll();
        for (Playlist playlist : playlists) {
            JButton button = new JButton(playlist.name);
            button.addActionListener(e -> loadVideoButtons(playlist));
            playlistPanel.add(button);
        }
        playlistPanel.revalidate();
        playlistPanel.repaint();
    }

    private void loadVideoButtons(Playlist playlist) {
        videoPanel.removeAll();
        for (Video video : playlist.videos) {
            JButton button = new JButton(video.title);
            button.addActionListener(e -> video.open());
            videoPanel.add(button);
        }
        videoPanel.revalidate();
        videoPanel.repaint();
    }

    private static List<Playlist> readPlaylistsFromTxt(String filePath) {
        List<Playlist> playlists = new ArrayList<>();
        try (BufferedReader br = new BufferedReader(new FileReader(filePath))) {
            int total = Integer.parseInt(br.readLine().trim());
            for (int i = 0; i < total; i++) {
                String name = br.readLine();
                String description = br.readLine();
                String rating = br.readLine();
                int videoCount = Integer.parseInt(br.readLine().trim());
                List<Video> videos = new ArrayList<>();
                for (int j = 0; j < videoCount; j++) {
                    String title = br.readLine();
                    String link = br.readLine();
                    videos.add(new Video(title, link));
                }
                playlists.add(new Playlist(name, description, rating, videos));
            }
        } catch (Exception e) {
            e.printStackTrace();
        }
        return playlists;
    }

    public static void main(String[] args) {
        SwingUtilities.invokeLater(() -> {
            VideoApp app = new VideoApp();
            app.setVisible(true);
        });
    }
}

package fr.univ_eiffel.domain;

import java.io.BufferedReader;
import java.io.FileReader;
import java.io.IOException;
import java.util.HashMap;
import java.util.HashSet;
import java.util.Map;
import java.util.Set;

public class BrickRegistry {
    private static final Set<LegoBrick> bricks = new HashSet<>();

    public static void loadLegoBrick(String filePath) throws IOException {
        try (BufferedReader br = new BufferedReader(new FileReader(filePath))) {
            String line;
            br.readLine();
            while ((line = br.readLine()) != null) {
                bricks.add(new LegoBrick(line));
            }
        }
        System.out.println(bricks.size() + " lego chargées en mémoire.");
    }

    public static Set<LegoBrick> getBricks() { return Set.copyOf(bricks); }

}

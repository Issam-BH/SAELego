package fr.univ_eiffel.domain;

import java.io.BufferedReader;
import java.io.FileReader;
import java.io.IOException;
import java.util.HashMap;
import java.util.Map;
import java.util.Set;

public class ColorRegistry {
    private static final Map<Integer, LegoColor> colorsById = new HashMap<>();

    public static void loadColors(String csvPath) throws IOException {
        try (BufferedReader br = new BufferedReader(new FileReader(csvPath))) {
            String line;
            br.readLine();
            while ((line = br.readLine()) != null) {
                String[] parts = line.split(",");
                if (parts.length < 3) continue;
                try {
                    int id = Integer.parseInt(parts[0]);
                    String name = parts[1];
                    String hex = parts[2];
                    colorsById.put(id, new LegoColor(id, name, hex));
                } catch (NumberFormatException e) {
                    //Ignore error
                }
            }
        }
        System.out.println(colorsById.size() + " couleurs chargées en mémoire.");
    }

    public static LegoColor getColor(int id) { return colorsById.get(id); }

    public static Set<Integer> getAllIds() { return colorsById.keySet(); }
}

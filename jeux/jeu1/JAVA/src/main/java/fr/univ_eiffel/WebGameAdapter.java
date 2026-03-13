package fr.univ_eiffel;

import java.io.BufferedReader;
import java.io.FileReader;
import java.util.ArrayList;
import java.util.Collections;
import java.util.List;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

public class WebGameAdapter {
    public static void main(String[] args) {
        // Chemin vers le dossier contenant les fichiers générés par le C
        String dir = "sae_java_group/";
        
        try {
            // 1. Lire image.txt pour générer le tableau JSON 'targetGrid'
            BufferedReader imgReader = new BufferedReader(new FileReader(dir + "image.txt"));
            String[] dims = imgReader.readLine().trim().split("\\s+");
            int width = Integer.parseInt(dims[0]);
            int height = Integer.parseInt(dims[1]);
            
            StringBuilder json = new StringBuilder("{\n  \"targetGrid\": [\n");
            Pattern hexPattern = Pattern.compile("[0-9a-fA-F]{6}"); // Extrait les codes couleurs 
            
            for (int y = 0; y < height; y++) {
                String line = imgReader.readLine();
                if (line == null) break;
                
                json.append("    [");
                Matcher m = hexPattern.matcher(line);
                boolean first = true;
                while (m.find()) {
                    if (!first) json.append(", ");
                    json.append("{\"color\": \"#").append(m.group()).append("\"}");
                    first = false;
                }
                json.append("]");
                if (y < height - 1) json.append(",");
                json.append("\n");
            }
            imgReader.close();
            json.append("  ],\n");

            // 2. Lire outAnyShape.txt pour générer le tableau JSON 'bricksQueue'
            BufferedReader solReader = new BufferedReader(new FileReader(dir + "outAnyShape.txt"));
            solReader.readLine(); // Ignore la 1ère ligne (statistiques du C)
            
            List<String> bricks = new ArrayList<>();
            String line;
            while ((line = solReader.readLine()) != null) {
                if (line.trim().isEmpty()) continue;
                String[] parts = line.trim().split("\\s+");
                if (parts.length < 4) continue;
                
                String[] shapeColor = parts[0].split("/");
                String shape = shapeColor[0].split("-")[0]; // On supprime les informations de trous éventuels
                String color = "#" + shapeColor[1];
                
                bricks.add(String.format("{\"shape\": \"%s\", \"color\": \"%s\"}", shape, color));
            }
            solReader.close();
            
            // On mélange l'ordre des briques pour le jeu
            Collections.shuffle(bricks);
            
            json.append("  \"bricksQueue\": [\n");
            for (int i = 0; i < bricks.size(); i++) {
                json.append("    ").append(bricks.get(i));
                if (i < bricks.size() - 1) json.append(",");
                json.append("\n");
            }
            json.append("  ]\n}");
            
            // 3. Imprimer le JSON final dans la console (capturé par Node.js)
            System.out.println(json.toString());
            
        } catch (Exception e) {
            System.err.println("{\"error\": \"" + e.getMessage() + "\"}");
            System.exit(1);
        }
    }
}
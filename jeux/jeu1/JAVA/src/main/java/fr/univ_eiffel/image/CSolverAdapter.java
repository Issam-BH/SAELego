package fr.univ_eiffel.image;

import java.io.BufferedReader;
import java.io.File;
import java.io.FileReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.util.HashMap;
import java.util.Map;

public class CSolverAdapter {

    private static final String EXE_PATH = "pavage"; // Use pavage
    private static final String SOLUTION_FILE = "outV3.txt";

    public void runSolver(String workDir) throws IOException, InterruptedException {
        File dir = new File(workDir);
        
        // Command: pavage
        ProcessBuilder pb = new ProcessBuilder("./" + EXE_PATH, ".");
        pb.directory(dir);
        pb.redirectErrorStream(true);

        System.out.println("Start C solver...");
        Process process = pb.start();

        // Enter logs C in console Java
        try (BufferedReader reader = new BufferedReader(new InputStreamReader(process.getInputStream()))) {
            String line;
            while ((line = reader.readLine()) != null) {
                System.out.println("[C]: " + line);
            }
        }

        int exitCode = process.waitFor();
        if (exitCode != 0) {
            System.err.println("C solver error: " + exitCode);
        } else {
            System.out.println("C solver finished successfully.");
        }
    }

    public Map<String, Integer> parseSolution(String workDir) throws IOException {
        File file = new File(workDir, SOLUTION_FILE);
        Map<String, Integer> orderMap = new HashMap<>();

        if (!file.exists()) {
            System.out.println("File not found: " + file.getAbsolutePath());
            return orderMap;
        }

        try (BufferedReader br = new BufferedReader(new FileReader(file))) {
            // First line is header, skip it
            br.readLine(); 

            String line;
            while ((line = br.readLine()) != null) {
                // Format C: 2x4/fc97ac 10 20 0
                // We need to extract "2x4/fc97ac"
                String[] parts = line.split(" ");
                if (parts.length > 0) {
                    String brickRef = parts[0]; 
                    // brickRef has the format "Size/Color" or "Size-Mask/Color"
                    // We need to count their occurrences
                    orderMap.put(brickRef, orderMap.getOrDefault(brickRef, 0) + 1);
                }
            }
        }
        return orderMap;
    }
}
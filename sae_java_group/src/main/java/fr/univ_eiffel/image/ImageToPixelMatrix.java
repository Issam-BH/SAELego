package fr.univ_eiffel.image;

import javax.imageio.ImageIO;
import java.awt.image.BufferedImage;
import java.io.File;
import java.util.ArrayList;
import java.util.List;

public class ImageToPixelMatrix {

    private static List<String> pixelMatrix;
    private static int height;
    private static int width;

    public static void transformImageToMatrix(String pathToFile) {
        try {
            File file = new File(pathToFile);
            BufferedImage image = ImageIO.read(file);

            width = image.getWidth();
            height = image.getHeight();

            pixelMatrix = new ArrayList<String>();

            for (int y = 0; y < height; y++) {
                for (int x = 0; x < width; x++) {
                    int pixel = image.getRGB(x, y);
                    pixelMatrix.add(String.format("%08X", pixel));
                }
            }

        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    //Take BufferedImage as input and set pixelMatrix
    public static void setPixelMatrix(BufferedImage image) {
        if (image == null) return;
        
        width = image.getWidth();
        height = image.getHeight();
        pixelMatrix = new ArrayList<>();

        for (int y = 0; y < height; y++) {
            for (int x = 0; x < width; x++) {
                int pixel = image.getRGB(x, y);
                // Форматуємо у Hex ARGB
                pixelMatrix.add(String.format("%08X", pixel));
            }
        }
    }

    public static void printPixelMatrix() {
        if (pixelMatrix == null) {
            System.out.println("The matrix is empty.");
            return;
        }

        for (int y = 0; y < height; y++) {
            StringBuilder pixelRow = new StringBuilder();
            for (int x = 0; x < width; x++) {
                pixelRow.append(pixelMatrix.get(y*width+x)).append(" ");
            }
            System.out.println(pixelRow);
        }
    }

    public static void writeMatrixToFile(String filePath) {
        if (pixelMatrix == null) {
            System.out.println("The matrix is empty.");
            return;
        }

        try (java.io.BufferedWriter writer = new java.io.BufferedWriter(new java.io.FileWriter(filePath))) {
            writer.write(height+" "+width);
            writer.newLine();
            for (int y = 0; y < height; y++) {
                StringBuilder row = new StringBuilder();
                for (int x = 0; x < width; x++) {
                    row.append(pixelMatrix.get(y * width + x)).append(" ");
                }
                writer.write(row.toString().trim());
                writer.newLine();
            }
        } catch (Exception e) {
            System.err.println("Error writing matrix to file.");
        }
    }

    public static void writeImageForC(String filePath) {
        if (pixelMatrix == null) {
            System.out.println("Matrix is empty.");
            return;
        }

        try (java.io.BufferedWriter writer = new java.io.BufferedWriter(new java.io.FileWriter(filePath))) {
            // C wait: Width Height
            writer.write(width + " " + height);
            writer.newLine();

            for (String hex : pixelMatrix) {
                // hex for us AARRGGBB, we need to take only last 6 digits (RRGGBB)
                String rgb = hex.length() > 6 ? hex.substring(hex.length() - 6) : hex;
                writer.write(rgb + " ");
            }
            System.out.println("File " + filePath + " generated for C.");
        } catch (Exception e) {
            e.printStackTrace();
        }
    }
}

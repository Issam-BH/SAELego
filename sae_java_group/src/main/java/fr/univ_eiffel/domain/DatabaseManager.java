package fr.univ_eiffel.domain;

import java.io.BufferedWriter;
import java.io.FileWriter;
import java.sql.*;
import java.util.ArrayList;
import java.util.List;

public class DatabaseManager {

    private static final String URL = "jdbc:mysql://localhost:3306/lego_app";
    private static final String USER = "admin";
    private static final String PASSWORD = "admin";

    private Connection connect() throws SQLException {
        return DriverManager.getConnection(URL, USER, PASSWORD);
    }

    public void importColorsToDb(String csvPath) {
        try {
            try (Connection conn = connect();
                 ResultSet rs = conn.createStatement().executeQuery("SELECT COUNT(*) FROM color")) {
                if (rs.next() && rs.getInt(1) > 0) return; // Déjà rempli
            }

            ColorRegistry.loadColors(csvPath);
            String sql = "INSERT INTO color (color_id, hex_code, color_name) VALUES (?, ?, ?)";

            try (Connection conn = connect();
                 PreparedStatement pstmt = conn.prepareStatement(sql)) {

                for (int id : ColorRegistry.getAllIds()) {
                    LegoColor c = ColorRegistry.getColor(id);
                    pstmt.setInt(1, c.getId());
                    pstmt.setString(2, c.getHex());
                    pstmt.setString(3, c.getName());
                    pstmt.addBatch();
                }
                pstmt.executeBatch();
                System.out.println("Couleurs synchronisées en BDD.");
            }
        } catch (Exception e) { e.printStackTrace(); }
    }

    public void importBrickToDb(String filePath) {
        try {
            try (Connection conn = connect();
                 ResultSet rs = conn.createStatement().executeQuery("SELECT COUNT(*) FROM brick_spec")) {
                if (rs.next() && rs.getInt(1) > 0) return; // Déjà rempli
            }

            BrickRegistry.loadLegoBrick(filePath);
            String sql = "INSERT INTO brick_spec (name,width,lenght) VALUES (?, ?, ?)";

            try (Connection conn = connect();
                 PreparedStatement pstmt = conn.prepareStatement(sql)) {

                for (LegoBrick brick : BrickRegistry.getBricks()) {
                    pstmt.setString(1, brick.getRawName());
                    pstmt.setInt(2, brick.getWidth());
                    pstmt.setInt(3, brick.getLength());
                    pstmt.addBatch();
                }
                pstmt.executeBatch();
                System.out.println("Legos synchronisées en BDD.");
            }
        } catch (Exception e) { e.printStackTrace(); }
    }

    public void addStock(String brickRef, int colorId, int qtyToAdd) throws SQLException {
        String getIdPiece = "SELECT spec_id FROM brick_spec WHERE name LIKE ?";
        String updateStock = "INSERT INTO stock (stock_id,quantity) VALUES (?, ?) " +
                "ON DUPLICATE KEY UPDATE quantity = quantity + ?";
        int id = 0;
        try (Connection conn = connect();
             PreparedStatement pstmt = conn.prepareStatement(getIdPiece)) {
            pstmt.setString(1, brickRef);
            ResultSet rs = pstmt.executeQuery();
            if (rs.next()) {
                id = rs.getInt("spec_id");
            }
        } catch (SQLException e) { e.printStackTrace(); }
        try (Connection conn = connect();
             PreparedStatement pstmt2 = conn.prepareStatement(updateStock)) {
            pstmt2.setInt(1, id);
            pstmt2.setInt(2, qtyToAdd);
            pstmt2.setInt(3, qtyToAdd);
            pstmt2.executeUpdate();
            System.out.println("Stock mis à jour : " + qtyToAdd + " x " + brickRef);
        } catch (SQLException e) { e.printStackTrace(); }
    }

    //Export catalog for C program to use
    public void exportCatalogForC(String filename) {
        try (Connection conn = connect();
             BufferedWriter writer = new BufferedWriter(new FileWriter(filename))) {

            // Take shapes from brick_spec
            List<String> shapes = new ArrayList<>();
            try (Statement stmt = conn.createStatement();
                 ResultSet rs = stmt.executeQuery("SELECT * FROM brick_spec")) {
                while (rs.next()) {
                    // C wait format "Width-Length"
                    shapes.add(rs.getInt("width") + "-" + rs.getInt("lenght"));
                }
            }

            // Take colors from color table
            List<String> colors = new ArrayList<>();
            try (Statement stmt = conn.createStatement();
                 ResultSet rs = stmt.executeQuery("SELECT * FROM color")) {
                while (rs.next()) {
                    colors.add(rs.getString("hex_code"));
                }
            }

            // Generate bricks combinations
            List<String> brickLines = new ArrayList<>();
            int brickCount = 0;
            
            // i = index form in the file C starting from 0
            // j = index color in the file C 
            for (int i = 0; i < shapes.size(); i++) {
                for (int j = 0; j < colors.size(); j++) {
                    brickLines.add(i + "/" + j + " 10 999");
                    brickCount++;
                }
            }

            // Write to file
            // Header: nShapes nColors nBricks
            writer.write(shapes.size() + " " + colors.size() + " " + brickCount);
            writer.newLine();

            // List of shapes: 2-4 1-1 ...
            for (String s : shapes) writer.write(s + " ");
            writer.newLine();

            // List of colors: FFFFFF 000000 ...
            for (String c : colors) writer.write(c + " ");
            writer.newLine();

            // List of bricks
            for (String b : brickLines) {
                writer.write(b + " ");
            }

            System.out.println("Файл " + filename + " згенеровано для С.");

        } catch (Exception e) {
            e.printStackTrace();
        }
    }
}

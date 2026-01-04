package fr.univ_eiffel;

import fr.univ_eiffel.domain.DatabaseManager;
import fr.univ_eiffel.factory.OrderService;
import fr.univ_eiffel.image.BicubicStrategy;
import fr.univ_eiffel.image.ImageResizer;
import fr.univ_eiffel.image.ImageToPixelMatrix;
import fr.univ_eiffel.image.CSolverAdapter;

import javax.imageio.ImageIO;
import java.awt.image.BufferedImage;
import java.io.File;
import java.util.Map;

//TIP To <b>Run</b> code, press <shortcut actionId="Run"/> or
// click the <icon src="AllIcons.Actions.Execute"/> icon in the gutter.
public class Main {
    private static final String EMAIL = "issam.ben-hamouda@edu.univ-eiffel.fr";
    private static final String API_KEY = "a5fdee983a8bf4d350b61238600938fdb188a5b3cc9351168ec1bdce2dfc403e";

    public static void main(String[] args) throws Exception {
        // Database setup
        DatabaseManager db = new DatabaseManager();
        db.importColorsToDb("colors.csv");
        db.importBrickToDb("bricks.txt"); 
        
        // Image processing
        System.out.println("--- Image processing ---");
        File imgFile = new File("test_image.jpg");
        BufferedImage original = ImageIO.read(imgFile);
        
        // Generate pixel matrix from image
        ImageResizer imgRes = new ImageResizer(new BicubicStrategy());
        BufferedImage resizedImg = imgRes.resizeImage(original, 32, 32); 
        ImageToPixelMatrix.setPixelMatrix(resizedImg); 

        // Prepare files for C solver
        System.out.println("--- Prepare files for C solver---");
        // Write image.txt
        ImageToPixelMatrix.writeImageForC("image.txt");
        // Write briques.txt (export from DB)
        db.exportCatalogForC("briques.txt");

        // Run C solver
        System.out.println("--- Run C solver ---");
        CSolverAdapter solver = new CSolverAdapter();
        solver.runSolver("."); 

        // Analyze results
        System.out.println("--- Analyze results ---");
        Map<String, Integer> itemsToOrder = solver.parseSolution(".");
        
        System.out.println("Need to order:");
        itemsToOrder.forEach((k, v) -> System.out.println(k + " : " + v));

        // Place order
        if (!itemsToOrder.isEmpty()) {
            System.out.println("--- Place order ---");
            OrderService orders = new OrderService(EMAIL, API_KEY);
            
            
            String quoteId = orders.requestQuote(itemsToOrder); 
            if (quoteId != null) {
                System.out.println("Devis: " + quoteId);
                if (orders.confirmOrder(quoteId)) {
                    System.out.println("Order confirmed!");
                    // Update stock in DB
                    for (Map.Entry<String, Integer> entry : itemsToOrder.entrySet()) {
                         // db.addStock(...) - implement this method in DatabaseManager
                    }
                }
            }
        }
    }
}
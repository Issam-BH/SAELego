package fr.univ_eiffel.image;
import java.awt.image.BufferedImage;

public class BicubicStrategy implements ResolutionStrategy {

    @Override
    public String getName() {
        return "Interpolation Bicubique";
    }

    @Override
    public BufferedImage resize(BufferedImage source, int targetW, int targetH) {
        BufferedImage dest = new BufferedImage(targetW, targetH, BufferedImage.TYPE_INT_RGB);
        double ratioW = (double) source.getWidth() / targetW;
        double ratioH = (double) source.getHeight() / targetH;

        for (int y = 0; y < targetH; y++) {
            for (int x = 0; x < targetW; x++) {
                double srcX = x * ratioW;
                double srcY = y * ratioH;
                int xInt = (int) srcX;
                int yInt = (int) srcY;
                double u = srcX - xInt;
                double v = srcY - yInt;

                dest.setRGB(x, y, interpolatePixel(source, xInt, yInt, u, v));
            }
        }
        return dest;
    }

    private int interpolatePixel(BufferedImage img, int x, int y, double u, double v) {
        double[] r = new double[4];
        double[] g = new double[4];
        double[] b = new double[4];

        for (int m = -1; m <= 2; m++) {
            double rRow = 0, gRow = 0, bRow = 0;
            for (int n = -1; n <= 2; n++) {
                int px = Math.min(Math.max(x + n, 0), img.getWidth() - 1);
                int py = Math.min(Math.max(y + m, 0), img.getHeight() - 1);
                int rgb = img.getRGB(px, py);

                double weight = cubic(n - u);
                rRow += ((rgb >> 16) & 0xFF) * weight;
                gRow += ((rgb >> 8) & 0xFF) * weight;
                bRow += (rgb & 0xFF) * weight;
            }
            double weightY = cubic(m - v);
            r[m + 1] = rRow * weightY;
            g[m + 1] = gRow * weightY;
            b[m + 1] = bRow * weightY;
        }

        int finalR = clamp((int) (r[0] + r[1] + r[2] + r[3]));
        int finalG = clamp((int) (g[0] + g[1] + g[2] + g[3]));
        int finalB = clamp((int) (b[0] + b[1] + b[2] + b[3]));

        return (finalR << 16) | (finalG << 8) | finalB;
    }

    private double cubic(double x) {
        double a = -0.5;
        x = Math.abs(x);
        if (x <= 1.0) return (a + 2) * x * x * x - (a + 3) * x * x + 1;
        if (x < 2.0) return a * x * x * x - 5 * a * x * x + 8 * a * x - 4 * a;
        return 0;
    }

    private int clamp(int val) {
        return Math.max(0, Math.min(255, val));
    }
}
package fr.univ_eiffel.image;

import java.awt.image.BufferedImage;

public class BilinearStrategy implements ResolutionStrategy {

    @Override
    public String getName() { return "Bilinéaire"; }

    @Override
    public BufferedImage resize(BufferedImage source, int w2, int h2) {
        BufferedImage dest = new BufferedImage(w2, h2, BufferedImage.TYPE_INT_RGB);
        int w1 = source.getWidth();
        int h1 = source.getHeight();
        float x_ratio = ((float)(w1 - 1)) / w2;
        float y_ratio = ((float)(h1 - 1)) / h2;
        for (int i = 0; i < h2; i++) {
            for (int j = 0; j < w2; j++) {
                int x = (int)(x_ratio * j);
                int y = (int)(y_ratio * i);
                float x_diff = (x_ratio * j) - x;
                float y_diff = (y_ratio * i) - y;
                int pA = source.getRGB(x, y);
                int pB = source.getRGB(x + 1, y);
                int pC = source.getRGB(x, y + 1);
                int pD = source.getRGB(x + 1, y + 1);
                dest.setRGB(j, i, source.getRGB(x, y));
            }
        }
        return dest;
    }
}

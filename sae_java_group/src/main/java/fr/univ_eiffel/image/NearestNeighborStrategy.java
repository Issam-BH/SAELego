package fr.univ_eiffel.image;

import java.awt.image.BufferedImage;

public class NearestNeighborStrategy implements ResolutionStrategy {

    @Override
    public String getName() { return "Plus Proche Voisin"; }

    @Override
    public BufferedImage resize(BufferedImage source, int targetW, int targetH) {
        BufferedImage dest = new BufferedImage(targetW, targetH, BufferedImage.TYPE_INT_RGB);
        double ratioW = (double) source.getWidth() / targetW;
        double ratioH = (double) source.getHeight() / targetH;
        for (int x = 0; x < targetW; x++) {
            for (int y = 0; y < targetH; y++) {
                dest.setRGB(x, y, source.getRGB((int)(x * ratioW), (int)(y * ratioH)));
            }
        }
        return dest;
    }
}

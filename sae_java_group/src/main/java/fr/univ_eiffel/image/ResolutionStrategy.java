package fr.univ_eiffel.image;

import java.awt.image.BufferedImage;

public interface ResolutionStrategy {
    BufferedImage resize(BufferedImage source, int targetWidth, int targetHeight);
    String getName();
}
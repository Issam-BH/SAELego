package fr.univ_eiffel.image;

import java.awt.image.BufferedImage;

public class ImageResizer {

    private ResolutionStrategy strategy;

    public ImageResizer(ResolutionStrategy strategy) {
        this.strategy = strategy;
    }

    public void setChosenStrategy(ResolutionStrategy strategy) {
        this.strategy = strategy;
    }

    public String getChosenStrategy() {
        return strategy.getName();
    }

    public BufferedImage resizeImage(BufferedImage image, int width, int height) {
        return strategy.resize(image, width, height);
    }

}

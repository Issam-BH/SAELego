package fr.univ_eiffel.domain;

public class LegoBrick {
    private int width;
    private int length;
    private String rawName;

    public LegoBrick(String name) {
        this.rawName = name;
        parseName(name);
    }

    private void parseName(String name) {
        String[] parts = name.split("-");
        if (parts.length >= 2) {
            this.width = Integer.parseInt(parts[0]);
            this.length = Integer.parseInt(parts[1]);
        }
    }

    public String getRawName() { return rawName; }

    public int getLength() {
        return length;
    }

    public int getWidth() {
        return width;
    }
}
package fr.univ_eiffel.domain;

import java.awt.*;

public class LegoColor {
    private final int id;
    private final String name;
    private final String hex;
    private final Color javaColor;

    public LegoColor(int id, String name, String hex) {
        this.id = id;
        this.name = name;
        this.hex = hex;
        this.javaColor = new Color(Integer.parseInt(hex, 16));
    }

    public int getId() { return id; }
    public String getName() { return name; }
    public String getHex() { return hex; }
    public Color getJavaColor() { return javaColor; }

    @Override
    public String toString() { return name + " (#" + hex + ")"; }
}
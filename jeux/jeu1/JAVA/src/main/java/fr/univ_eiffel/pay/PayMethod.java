package fr.univ_eiffel.pay;

import java.io.IOException;

public interface PayMethod {
    void pay(double amount) throws IOException;
}
package fr.univ_eiffel.factory;

import java.nio.charset.StandardCharsets;
import java.security.KeyFactory;
import java.security.PublicKey;
import java.security.Signature;
import java.security.spec.X509EncodedKeySpec;
import java.util.Base64;
import java.util.HexFormat;

public class CryptoVerifier {
    public static boolean verifyBrick(String pieceName, String serialHex, String signatureHex, String publicKeyBase64) {
        try {
            byte[] keyBytes = Base64.getDecoder().decode(publicKeyBase64);
            X509EncodedKeySpec spec = new X509EncodedKeySpec(keyBytes);
            KeyFactory kf = KeyFactory.getInstance("Ed25519");
            PublicKey pubKey = kf.generatePublic(spec);

            byte[] nameBytes = pieceName.getBytes(StandardCharsets.US_ASCII);
            byte[] serialBytes = HexFormat.of().parseHex(serialHex);
            byte[] dataToVerify = new byte[nameBytes.length + serialBytes.length];
            System.arraycopy(nameBytes, 0, dataToVerify, 0, nameBytes.length);
            System.arraycopy(serialBytes, 0, dataToVerify, nameBytes.length, serialBytes.length);

            Signature sig = Signature.getInstance("Ed25519");
            sig.initVerify(pubKey);
            sig.update(dataToVerify);
            return sig.verify(HexFormat.of().parseHex(signatureHex));
        } catch (Exception e) { return false; }
    }
}
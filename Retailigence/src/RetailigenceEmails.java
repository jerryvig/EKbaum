import java.util.List;
import java.util.Properties;
import java.util.Date;
import java.util.Locale;
import java.io.BufferedWriter;
import java.io.FileWriter;
import java.io.IOException;
import java.text.SimpleDateFormat;
import javax.mail.Session;
import javax.mail.MessagingException;
import javax.mail.Store;
import javax.mail.Folder;
import javax.mail.Message;
import javax.mail.NoSuchProviderException;
import javax.mail.Address;
import javax.mail.internet.MimeMessage;
import java.text.SimpleDateFormat;

public final class RetailigenceEmails {
    public static void main( String[] args ) {

        BufferedWriter writer = null;
        try { writer = new BufferedWriter( new FileWriter("/tmp/OCTripAddresses.csv") ); }
        catch ( IOException ioe ) { ioe.printStackTrace(); }

	String host = "imap.sonic.net";
	String username = "esther@retailigence.com";
	String password = "pinkydog613";

	SimpleDateFormat myFormatter = new SimpleDateFormat( "yyyy-MM-dd", Locale.US );

	Properties props = System.getProperties();
	props.setProperty( "mail.store.protocol", "imaps" );

        try {
	    Session sess = Session.getDefaultInstance( props, null );
            Store store = sess.getStore("imaps");
            store.connect( "imap.gmail.com", username, password );

            System.out.println( store );

            

            Folder defFolder = store.getFolder("[Gmail]").getFolder("All Mail");
            defFolder.open(Folder.READ_ONLY);
            Message messages[] = defFolder.getMessages();
            System.out.println( Integer.toString(messages.length) );
            for ( int i=0; i<messages.length; i++ ) {
	       Address addys[] = messages[i].getReplyTo();
               for ( int j=0; j<addys.length; j++ ) {
		  writer.write( addys[j].toString().trim() + "\n" ); 
               }
               try {
                Address[] recipients = messages[i].getAllRecipients();
                for ( int j=0; j<recipients.length; j++ ) {
		   writer.write( recipients[j].toString().trim() + "\n" );
                }
               } catch ( NullPointerException sqle ) { sqle.printStackTrace(); }
	    }

	} catch ( MessagingException e ) { e.printStackTrace(); }
        catch ( IOException ioe ) { ioe.printStackTrace(); }
    }
}	

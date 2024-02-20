# Υποχρεωτική Εργασία - Ηλεκτρονικό Επιχειρείν
## Αναλυτική Περιγραφή της εργασίας στο Αναφορά.pdf
## Τρόπος Εκτέλεσης 
### Βήμα 1:
Τοποθετούμε τον φάκελο `Kostas_Armando_E20073` στον φάκελο `htdocs` του XAMPP.

### Βήμα 2:
Για να εισάγουμε τις συλλογές, χρειαζόμαστε το Database Tools. Ανοίγουμε ένα terminal (ή την εντολική γραμμή του Windows) και εκτελούμε τις παρακάτω εντολές:

```bash
mongoimport --db Store --collection orders --jsonArray --file C:\xampp\htdocs\Kostas_Armando_E20073\my_db\orders.json
mongoimport --db Store --collection products --jsonArray --file C:\xampp\htdocs\Kostas_Armando_E20073\my_db\products.json
mongoimport --db Store --collection users --jsonArray --file C:\xampp\htdocs\Kostas_Armando_E20073\my_db\users.json
mongoimport --db Store --collection survey --jsonArray --file C:\xampp\htdocs\Kostas_Armando_E20073\my_db\survey.json
```

### Βήμα 3:
Ανοίγουμε το XAMPP και εκτελούμε τον Apache server.

### Βήμα 4:
Στον browser μας, επισκεπτόμαστε τον παρακάτω σύνδεσμο: http://localhost/Kostas_Armando_E20073/my_files/interfaces/

### Βήμα 5:
Στον browser μας, επισκεπτόμαστε τον παρακάτω σύνδεσμο: http://localhost/Kostas_Armando_E20073/my_files/interfaces/users.html

Συνδεόμαστε ως διαχειριστής με τα παρακάτω στοιχεία:
Όνομα Χρήστη: admin
Κωδικός: 12345678

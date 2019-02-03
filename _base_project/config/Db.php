<?php
class Db {
    public function __construct() { /** */ }
    private static function getDb() {
        try {
            // Essaie de faire ce script...
            $bdd = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8;port='.DB_PORT, DB_USER, DB_PWD);
        }
        catch (Exception $e) {
            // Sinon, capture l'erreur et affiche la
            die('Erreur : ' . $e->getMessage());
        }
        return $bdd;
    }
    /**
     * Permet d'enregistrer (INSERT) des données en base de données.
     * @param string    $table  Nom de la table dans lequel faire un INSERT
     * @param array     $data   Array contenant en clé les noms des champs de la table, en valeurs les values à enregistrer
     * 
     * @return int      Id de l'enregistrement.
     * 
     * Exemple :
     * $table = "Category";
     * $data = [
     *      'title'         => "Nouvelle catégorie",
     *      'description'   => 'Ma nouvelle catégorie.',
     * ];
     */
    protected static function dbCreate(string $table, array $data) {
        $bdd = self::getDb();
        // Construction de la requête au format : INSERT INTO $table($data.keys) VALUES(:$data.keys) 
        $req  = "INSERT INTO " . $table;
        $req .= " (`".implode("`, `", array_keys($data))."`)";
        $req .= " VALUES (:".implode(", :", array_keys($data)).") ";
        $response = $bdd->prepare($req);
        var_dump($req, $data);
        $response->execute($data);
        return $bdd->lastInsertId();
    }
    /**
     * Permet de supprimer (DELETE) des données en base de données.
     * @param string    $table  Nom de la table dans lequel faire un DELETE
     * @param array     $data   Array contenant en clé la PK de la table, en value la valeur à donner.
     * 
     * @return void
     * 
     * Exemple: 
     * $table = "Movie";
     * $data = [ 'id' => 3 ];
     */
    protected static function dbDelete(string $table, array $data) {
        $bdd = self::getDb();
        // Construction de la requête au format : INSERT INTO $table($data.keys) VALUES(:$data.keys) 
        $req  = "DELETE FROM " . $table . " WHERE " . array_keys($data)[0] . " = :" . array_keys($data)[0];
        $response = $bdd->prepare($req);
        $response->execute($data);
        return;
    }
    /**
     * Permet de récupérer (SELECT) des données en base de données.
     * @param string    $table  Nom de la table dans lequel faire un SELECT
     * @param array     $request   Array contenant une liste de trios ["champ", "opérateur", "valeur"].
     * 
     * @return array    Données demandées.
     * 
     * Exemple: 
     * $table = "Movie";
     * $request = [
     *      [ 'title', "like",'Rocky' ],
     *      [ 'realease_date', '>', '2000-01-01']
     * ];
     */
    protected static function dbFind(string $table, array $request = null) {
        $bdd = self::getDb();
        $req = "SELECT * FROM " . $table;
        if (isset($request)) {
            $req .= " WHERE ";
            $reqOrder = '';
            foreach($request as $r) {
                switch($r[0]):
                    case "orderBy":
                        $reqOrder = " ORDER BY `" . htmlspecialchars($r[1]) . "` " . htmlspecialchars($r[2]);
                        break;
                    
                    default:
                        $req .= "`". htmlspecialchars($r[0]) . "` " . htmlspecialchars($r[1]) . " '" . htmlspecialchars($r[2]) . "'";
                        $req .= " AND ";
                endswitch;
                
            }
            $req = substr($req, 0, -5);
            $req .= $reqOrder;
        }
        $response = $bdd->query($req);
        $data = [];
        while ($donnees = $response->fetch()) {
            $data[] = $donnees;
        }
        return $data;
    }
    /**
     * Permet de mettre à jour (UPDATE) des données en base de données.
     * @param string    $table  Nom de la table dans lequel faire un UPDATE
     * @param array     $data   Array contenant en clé les noms des champs de la table, en valeurs les values à enregistrer.
     * 
     * @return int      Id de l'élément modifié.
     * 
     * OBLIGATOIRE : Passer un champ 'id' dans le tableau 'data'.
     * 
     * Exemple :
     * $table = "Category";
     * $data = [
     *      'id'            => 4,
     *      'title'         => "Nouveau titre de catégorie",
     *      'description'   => 'Ma nouvelle catégorie.',
     * ];
     */
    protected static function dbUpdate(string $table, array $data, string $idField = null) {
        $bdd = self::getDb();
        $req  = "UPDATE " . $table . " SET ";
        $whereIdString = '';
        /**
         * Set du WHERE
         */
        $whereIdString = ($idField !== null) ? " WHERE `" . $idField . "` = :" . $idField : " WHERE id = :id";
        /**
         * Set des key = :value
         */
        foreach($data as $key => $value) {
            
            if ($key !== 'id') {
                $req .= "`" . $key . "` = :" . $key . ", ";
            }
        }
        $req = substr($req, 0, -2);
        $req .= $whereIdString;
        $response = $bdd->prepare($req);
        $response->execute($data);
        return $bdd->lastInsertId();
    }
}
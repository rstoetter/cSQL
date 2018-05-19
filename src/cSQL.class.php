<?php


/**
  *
  * The class cSQL helps to manage SQL statements. The namespace is rstoetter\cSQL.
  *
  * @author Rainer Stötter
  * @copyright 2016-2017 Rainer Stötter
  * @license MIT
  * @version =1.0
  *
  */

namespace rstoetter\cSQL;  
  




if ( (int) phpversion( ) < 5 ) die("Sorry, The class cSQL is written for PHP 5");

// define( '_SQL_GENERATOR_QUERY_TYPE_SELECT', 'SELECT' );
// define( '_SQL_GENERATOR_QUERY_TYPE_INSERT', 'INSERT' );
// define( '_SQL_GENERATOR_QUERY_TYPE_UPDATE', 'UPDATE' );
// define( '_SQL_GENERATOR_QUERY_TYPE_DELETE', 'DELETE' );
  


class cSQL {

	/**
	 * The version of the class
	 *
	 * @var boolean
	 */

	const VERSION = "1.0";
	

	
    /**
      *
      * The method IsSubquerySQL( ) returns true, if the query $sql is a subquery
      *
      * Example:
      *
      *
      * @param string $sql the query string
      * @return bool true, if the query $sql is a subquery
      *
      */    	
	
	
    static public function IsSubquerySQL( string & $sql ) : bool {

	// return ( substr( trim( $sql ), 0, 1 ) == '(' ) && ( substr( trim( $sql ), strlen( $sql ) - 1, 1  ) == ')' );
	// TODO IsSubquerySQL( ) auf letzte Klammer prüfen und dabei eventuellen Alias berücksichtigen
	
        $ret = false;

        if ( substr( trim( $sql ), 0, 1 ) == '(' ) {
            if ( ( stripos( trim( substr( trim( $sql ), 1 ) ) , 'SELECT' ) ) === 0 ) {
                $str = \rstoetter\cSQL\cSQL::RemoveAlias( $sql );
                $ret = ( substr( $str, -1, 1 ) == ')' );
            }
        }
        
        return $ret;

    }	// function IsSubquerySQL( )
    

    
    /**
      *
      * The method is_ctype_identifier( ) returns true, if $chr is a valid character for identifiers
      * The method takes into consideration, too, whether $chr is an extra identifier
      *
      * Example:
      *
      * @param string $chr is the character to test
      * @return bool true, if $chr is a valid character for identifiers
      *
      */     
	

	static private function is_ctype_identifier( string $chr ) : bool {
	
        if ( $chr == '' ) return false;

	    return 
                ( $chr == '_' ) || 
                ( ctype_alnum( $chr ) || 
                self::IsExtraIdentifier( $chr ) || 
                self::is_ctype_sonderzeichen( $chr ) 
        ) ;

	}	// function is_ctype_identifier( )
	
    /**
      *
      * The method is_ctype_sonderzeichen( ) returns true, if $chr is a valid character for identifiers and a country-specific character
      *
      * Example:
      *
      * @param string $chr is the character to test
      * @return bool true, if $chr is a valid character for identifiers
      *
      */    	
	
	static private function is_ctype_sonderzeichen( string $chr ) : bool {
	
            return ( strpos ( 'äöüßÄÖÜ', $chr ) !== false );	
	
	}
	
	
    /**
      *
      * The method is_ctype_dbfield( ) returns true, if $chr is a valid character for database fields
      *
      * Example:
      *
      * @param string $chr is the character to test
      * @return bool true, if $chr is a valid character for a database field
      *
      */     	

	static private function is_ctype_dbfield( string $chr ) : bool {
	    // mit dem Schema und oder Tabellennamen
	    return 
            ( $chr == '.' ) || 
            ( $chr == '_' ) || 
            ( 
                ctype_alnum( $chr ) || 
                self::is_ctype_sonderzeichen( $chr )  
            ) ;

	}	// function is_ctype_identifier( )
	
	static $m_id_extra = '';
	static $m_id_start_extra = '';
	
    /**
      *
      * The method SetExtraStartIdentifier( ) sets the active start characters of identifiers
      *
      * Example:
      *
      * @param string $str the string with the extra start for identifiers
      */   	    

      
    static public function SetExtraStartIdentifier( $str ) : string {
    
        $ret = self::$m_id_start_extra;

        self::$m_id_start_extra = $str;
        
        return $ret;

    }	// function SetExtraStartIdentifier( )	
	
    /**
      *
      * The method SetExtraIdentifier( ) sets the active characters of identifiers
      *
      * Example:
      *
      * @param string $str the string with the extra characters for identifiers
      */   	    
    

    static public function SetExtraIdentifier( string $str ) : string {

        $ret = self::$m_id_extra;
    
        self::$m_id_extra = $str;
        
        return $ret;

    }	// function SetExtraIdentifier( )
    
    /**
      *
      * The method IsExtraIdentifier( ) returns true, if $chr is an extra character for identifiers
      *
      * Example:
      *
      * @param string $chr the charecter to test
      *
      * @return bool true, if $chr is part of the extra characters of identifiers
      *
      */   	    
    


    static protected function IsExtraIdentifier( $chr ) {

        for ( $i = 0; $i < strlen( self::$m_id_extra ); $i++ ) {

            if ( substr( self::$m_id_extra, $i , 1 ) == $chr ) {
                return true;
            }

        }

        return false;

    }	// function IsExtraIdentifier( )    
    
    
    /**
      *
      * The method IsExtraStartIdentifier( ) returns true, if $chr is an extra start identifier
      *
      * Example:
      *
      * @param string $chr the charecter to test
      *
      * @return bool true, if $chr is part of the extra start characters of identifiers
      *
      */   	    


    static public function IsExtraStartIdentifier( string $chr ) : bool {

        for ( $i = 0; $i < strlen( self::$m_id_start_extra ); $i++ ) {

            if ( substr( self::$m_id_start_extra, $i , 1 ) == $chr ) return true;

        }

        return false;

    }	// function IsExtraStartIdentifier( )	


    /**
      *
      * The method is_ctype_identifier_start( ) returns true, if $chr is a valid starting character for identifiers
      * The method takes into consideration, too, whether $chr is an extra start identifier
      *
      * Example:
      *
      * @param string $chr is the character to test
      * @return bool true, if $chr is a valid starting character for identifiers
      *
      */     
	
	
	static private function is_ctype_identifier_start( string $chr ) : bool {

	    if ( $chr == '' ) return false;

	    return 
            ( $chr == '_' ) || 
            ( ctype_alpha( $chr ) ) || 
            self::IsExtraStartIdentifier( $chr ) || 
            self::is_ctype_sonderzeichen( $chr ) 
        ;

	}	// function is_ctype_identifier_start( )    
	
	static private function PositionEndingBraceSQL( 
        string & $str
	) : int {
	
        $ret = -1;
        $search1 = false;
        $search2 = false;
        $level = 0;
        
        // echo "\n PositionEndingBraceSQL( \"{$str}\" ) ";
        
        // skip spaces
        
        for ( $i = 0; $i < strlen( $str ); $i++ ) {
            if ( ! ctype_space( substr( $str, $i, 1 ) ) ) {
                break;
            }
        }
        
        if ( substr( $str, $i, 1 ) == '(' ) {     
        
            // echo "\n '(' detected";
        
            for ( $i = $i + 1 ; $i < strlen( $str ); $i++ ) {     // start with1 in order to skip first brace
            
                // echo "\n $i ->" . substr( $str, $i );
                
                if ( $search1 ) {
                    if ( substr( $str, $i, 1 ) != '"' ) {
                        continue;
                    }
                    $search1 = false;
                    continue;
                }
                
                if ( $search2 ) {
                    if ( substr( $str, $i, 1 ) != "'" ) {
                        continue;
                    }
                    $search2 = false;
                    continue;
                }
                
            
                if ( substr( $str, $i, 1 ) == '"' ) {
                    $search1 = true;                
                    continue;
                }
                
                if ( substr( $str, $i, 1 ) == "'" ) {
                    $search2 = true;                
                    continue;
                }       
                
                if ( substr( $str, $i, 1 ) == "(" ) {
                    // a new brace level was opened
                    $level++;    
                }             
            
                if ( substr( $str, $i, 1 ) == ')' ) {
                    if ( ! $level ) {
                        $ret = $i;
                        break;
                    } 
                    
                    $level --;
                    
                }
                
            }        
        
        }
        
        // echo "\n PositionEndingBraceSQL( ) returns $ret";
        
        return $ret;
	
	}  // function PositionEndingBraceSQL( )
    
    static public function PositionEndFunctionSQL( string & $sql ) : int {

	// return ( substr( trim( $sql ), 0, 1 ) == '(' ) && ( substr( trim( $sql ), strlen( $sql ) - 1, 1  ) == ')' );
	// TODO IsSubquerySQL( ) auf letzte Klammer prüfen und dabei eventuellen Alias berücksichtigen
	
        // echo "\n PositionEndFunctionSQL( \"{$sql}\" ) ";
	
        $ret = -1;
        
        $str = $sql;
        
        $i = 0;
        
        // skip spaces
        for ( $i = 0; $i < strlen( $str ); $i++ ) {
            if ( ! ctype_space( substr( $str, $i, 1 ) ) ) {
                break;
            }
        }
        
        // skip function name
        if ( self::is_ctype_identifier_start( substr( $str, $i, 1  ) ) ) {
            for ( $i = $i; $i < strlen( $str ); $i++ ) {
                if ( ! self::is_ctype_identifier( substr( $str, $i, 1 ) ) ) {
                    break;
                }
            }
        }
        
        $skipped_chars = $i;
        
        // echo "\n after function name with pos {$i} -> " . substr( $str, $i );
        
        if ( $i < strlen( $str ) ) {
            
            // skip '('
            // $i++;            
            
            $str = substr( $str, $i );
            
            // echo "\n start with " . $str;
        
            // skip  spaces
            for ( $i = 0; $i < strlen( $str ); $i++ ) {
                if ( ! ctype_space( substr( $str, $i, 1 ) ) ) {
                    break;
                }
            }
            
            // echo "\n without spaces ($i) : " . $str;
            
            if ( 
                ( $i < strlen( $str ) ) &&
                ( substr( $str, $i, 1  ) == '(' )
            ) {
                
                $str = substr( $str, $i );
                
                // echo "\n before PositionEndingBraceSQL: ( $i ) " . $str;
            
                // search closing brace of the function 
                if ( ( $pos = self::PositionEndingBraceSQL( $str ) ) != -1 ) {
                    $ret = $pos + $skipped_chars;
                }            
            
            }            
            
        }
        
        // echo "\n PositionEndFunctionSQL( ) returns {$ret}";
        
        return $ret;

    }	// function PositionEndFunctionSQL( )
    
    static public function IsFunctionSQL( string & $sql ) : bool {
    
        $ret = false;
        
        // echo "\n IsFunctionSQL( {$sql} ) ";
        
        $str = \rstoetter\cSQL\cSQL::RemoveAlias( $sql );
    
        if ( ( $pos = self::PositionEndFunctionSQL( $str ) ) != -1 ) {
        
            // echo "\n pos ')' is " . $pos . '->\'' . substr( $str, $pos ) . '\'';
        
            
            if ( $pos == strlen( $str ) -1 ) {
                $ret = true;
            } else {
            
            
                $str = trim( substr( $sql, $pos + 1 ) );
                if ( 
                    ( $str == '' ) ||
                    ( $str == ';' )
                ) {
                    $ret = true;
                }
            }
        }
        
        
        // echo "\n IsFunctionSQL( ) returns " . ( $ret ? 'true' : 'false' );
        
        return $ret;
    
    }   // function IsFunctionSQL( )
    
    static private function ContainsSpace( 
                            string $str
    ) : bool {		
    
        $ret = false;
    
        for ( $i = 0; $i < strlen( $str ); $i++ ) {
            if ( ctype_space( substr( $str, $i, 1 ) ) ) {
                $ret = true;
                break;
            }
        }
        
        return $ret;
    
    }   // function ContainsSpace( )    
    
	static public function CheckedFieldName( 
                            string $field_name,
                            bool $allow_subquery = true,
                            bool $allow_function = true
    ) : bool {	
	
        $ret = true;
        
        if ( ! strlen( trim( $field_name ) ) ) {
            return false;
        }
    
        /*
        echo 
            "\n CheckedFieldName( {$field_name} ," .
            ' allow_subquery = ' . ( $allow_subquery ? 'true' : 'false' ) . ',' .
            ' allow_function = ' . ( $allow_function ? 'true' : 'false' ) .
            "' ) "
        ;
        */
        
        if ( self::IsSubquerySQL( $field_name ) )  {
            $ret = $allow_subquery;
        } elseif ( self::IsFunctionSQL( $field_name ) )  {
            $ret = $allow_function;
        } else {
        
            $ret = ( self::ContainsSpace( $field_name ) == false );
            if ( ! $ret ) {
                // maybe an alias ?
                $field_name_pure = \rstoetter\cSQL\cSQL::RemoveAlias( $field_name );
                $ret = ( self::ContainsSpace( $field_name_pure ) == false );
            }
            
            if ( $ret ) {
                $ret = ( substr( $field_name, 0, 1 ) != '.' );
            }
            
            if ( $ret ) {
                $ret = ( substr( $field_name, -1, 1 ) != '.' );
            }            
        
        }
        
        // echo "\n CheckedFieldName( ) returns " . ( $ret ? 'true' : 'false' );
        
        return $ret;
	
	}  // function CheckedFieldName( )
    
    

    
    /**
      *
      * The method RemoveFunctions( ) removes all functions from the query string $expression. It strips any functions surrounding a column name and saves the alias, if any
      *
      * Example:
      *
      *
      * @param string $expression the query string
      * @return string the query string without the function calls
      *
      */    	
    

    static public function RemoveFunctions( $expression ) {	// TODO verschluckt optionalen ALIAS!

        // strip any functions surrounding a column name and save the alias, if any

        $expression = trim( $expression );
        $alias = '';

        $changed = false;
        if ( 
            ( substr( $expression, 0, 1 ) != '(' ) && 
            ( ( $pos1 = ( strpos( $expression, '(' ) ) ) !== false ) 
        ) {

            $alias = '';
            $start = -1;
            while ( ctype_alnum( $chr = substr( $expression, $start, 1 ) ) ) {

                $alias = $chr . $alias;
                $start--;

            }

            $pos2 = strrpos( $expression, ')' );

            assert( $pos2 !== false );

            if ( $pos2 !== false ) {


            $expression = trim( substr( $expression, $pos1 + 1, $pos2 -1 - ( $pos1 + 1 ) ) );

            $fertig = false;
            while ( !$fertig ) {

                $pos1 = strrpos( $expression, ','  );
                $pos2 = strrpos( $expression, ')'  );

                if ( ( $pos1 !== false  ) && ( $pos2 !== false ) ) {

                    if ( $pos1 > $pos2 ) {

                        $expression = trim( substr( $expression, 0, $pos1 - 1 ) );

                    } else {

                        $fertig = true;

                    }

                } elseif ( $pos1 !== false ) {

                    $expression = trim( substr( $expression, 0, $pos1 - 1 ) );
                    $fertig = true;


                } else {

                    $fertig = true;

                }

            }

            } else {
                assert( false == true );
            }

            $changed = true;

        }

        if ( $changed ) {
            $expression = self::RemoveFunctions( $expression ) ;
        }

        $alias = trim( $alias );
        if ( strlen( $alias ) ) {
            $alias = ' ' . $alias;
        }

        return $expression . $alias;


    }	// function RemoveFunctions( )



	/**
	 * The method AddFunction( ) adds a function to an expression part with a column name
	 *
	 * Example:
	 *
	 * cSQL::AddFunction( 'id_account as ia', 'TRIM( ' , ' )' );
	 *
	 * @param string $column_name the name of the column, which can have an alias, too
	 * @param string $function_pre holds the left part of the function
	 * @param string $function_post holds the right trailing part of the function
	 *
	 */
	
	
    static public function AddFunction( $column_name, $function_pre , $function_post ) {

        // AddFunction( \$col_name_original, 'TRIM( ' , ' )' );

        $alias = self::GetAliasFromTablename( $column_name );

        $rest = self::RemoveAlias( $column_name );

        $ret = $function_pre . $rest . $function_post;
        if ( strlen( $alias ) ) $ret .= ' as ' . $alias;

    // echo "<br>AddFunction() liefert '$ret'";

        return trim( $ret );

    }	// function AddFunction( )
    
	/**
	 * The method HasAlias( ) returns true, if the column name is accompagnied with an alias
	 *
	 * Example:
	 *
	 *
	 * @param string $column_name the name of the column, which can have an alias, too
	 * 
	 * @return bool true, if the column name is accompagnied with an alias
	 *
	 */
    

       static public function HasAlias( $column_name ) {

	    return ( strlen( self::GetAliasFromTablename( $column_name ) ) );

	}	// function HasAlias( )

	/**
	 * The method GetAliasFromTablename( ) returns the alias from the table name or column name in $table_name
	 *
	 * Example:
	 *
	 *
	 * @param string $table_name the name of the table or column, which can have an alias, too
	 * 
	 * @return string the alias from the table name or column name in $table_name or an empty string
	 *
	 */


    static public function GetAliasFromTablename( $table_name ) {

//  	cDebugUtilities::debug('GetAliasFromTablename() startet mit ', $table_name );

	$tst = trim( $table_name );

	if ( $tst == '' ) return '';
	if ( substr( $tst, strlen( $tst ) - 1, 1 ) == ')' ) return '';

	// muss der letzte Identifier sein

	$index = strlen( $tst );
	$id = '';

	for ( $i = $index; $i > 0; $i-- ) {

	    $ch = substr( $tst, $i, 1 );
	    if ( ctype_space( $ch )  ) {
		break;
	    }

	    $id = $ch . $id;

	}

	$tst = trim( substr( $tst, 0, $i ) );

	if ( $tst == '' ) {

	    return '';		// nur ein Identifier, also kein Alias
	}

	return trim( $id );

    }	// function GetAliasFromTablename( )


	/**
	 * The method RemoveAlias( ) removes the alias from the table name or column name in $table_name
	 *
	 * Example:
	 *
	 *
	 * @param string $table_name the name of the table or column, which can have an alias, too
	 * 
	 * @return string $table_name without the alias 
	 *
	 */


    static public function RemoveAlias( $table_name ) {	//TODO Alias kann auch ohne AS sein!

	// Remove AS from table declaration

	$ret = $table_name;
	$str = trim( $table_name );

// 	echo '<br>RemoveAlias() startet mit ' . $table_name;

	if ( $table_name == '' ) return '';
	if ( $str == '' ) return '';

	// ersten Identifier einlesen
	for ( $i = strlen( $str ); $i > 0; $i-- ) {

 	    $ch = substr( $str, $i, 1 );
	    if ( ctype_space( $ch ) ) break;

	}

	$str = trim( substr( $str, 0, $i ) );

	if ( $str == '' ) {
	    return $table_name;;
	}

	for ( $i = strlen( $str ); $i > 0; $i-- ) {

	    $ch = substr( $str, $i, 1 );

	    if ( ctype_space( $ch ) ) break;


	}


	if ( ( strtoupper( substr( $str, $i + 1 ) ) ) != 'AS' ) {

// 	    echo '<br>RemoveAlias() liefert ' . $str;

	    return trim( $str );

	}

// 	echo '<br>RemoveAlias() liefert ' . trim( substr( $str, 0, $i ) );;

	return trim( substr( $str, 0, $i ) );



    }	// function RemoveAlias( )


 
    

	
	
	/**
	 * The method FieldNameIn( ) returns the field name of the fully qualified column in $field_name_qualified
	 *
	 * Example:
	 *
	 *
	 * @param string $field_name_qualified the fully qualified field name
	 * 
	 * @return string the field name of the fully qualified column in $field_name_qualified without qualifications. If $field_name_qualified is a subquery then an empty string will be returned
	 *
	 */
	

	public static function FieldNameIn( $field_name_qualified ) {

	    if ( substr( trim( $field_name_qualified ), 0, 1 ) == '(' ) return '';


	    $pos = strrpos( $field_name_qualified, '.' );

	    if ( $pos == false ) return trim( $field_name_qualified );


	    return trim( substr( $field_name_qualified, $pos + 1 ) );


	}	// function FieldNameIn( )
	
	/**
	 * The method DotCount( ) returns the number of qualifying dots in the column or table name $str
	 *
	 * Example:
	 *
	 *
	 * @param string $str the fully qualified field or table name
	 * 
	 * @return int the number of qualifying dots in the column or table name $str
	 *
	 */
	

	public static function DotCount( $str ) {

	    $count = 0;

	    for ( $i = 0; $i < strlen( $str ); $i++) {

		if ( substr( $str, $i, 1 ) == '.' ) $count ++;

	    }

	    return $count;

	}	// function DotCount( )
	
	
	/**
	 * The method TableNameIn( ) returns the table name of the fully qualified field name in $field_name_qualified
	 *
	 * Example:
	 *
	 *
	 * @param string $field_name_qualified the fully qualified field name
	 * 
	 * @return string the table name of the fully qualified field name in $field_name_qualified
	 *
	 */
	

	public static function TableNameIn( $field_name_qualified ) {

	    $field_name_qualified = self::RemoveFunctions( $field_name_qualified );

	    if ( substr( trim( $field_name_qualified ), 0, 1 ) == '(' ) return '';

	    $pos = strrpos( $field_name_qualified, '.' );

	    if ( $pos == false ) return '';

	    $tst = substr( $field_name_qualified, 0, $pos );

	    $pos = strrpos( $tst, '.' );

	    if ( $pos == false ) return trim( $tst);

	    return trim( substr( $tst, $pos + 1 ) );

	}	// function TableNameIn( )
	
	
	/**
	 * The method SchemaNameIn( ) returns the schema name of the fully qualified field name in $field_name_qualified
	 *
	 * Example:
	 *
	 *
	 * @param string $field_name_qualified the fully qualified field name
	 * 
	 * @return string the schema name of the fully qualified field name in $field_name_qualified
	 *
	 */
	

	public static function SchemaNameIn( $field_name_qualified ) {

	    $field_name_qualified = self::RemoveFunctions( $field_name_qualified );

	    if ( substr( trim( $field_name_qualified ), 0, 1 ) == '(' ) return '';

	    $pos = strrpos( $field_name_qualified, '.' );

	    if ( $pos == false ) return '';

	    $tst = substr( $field_name_qualified, 0, $pos );

	    $pos = strrpos( $tst, '.' );

	    if ( $pos == false ) return '';

	    return substr( $tst, 0, $pos  );

	}	// function SchemaNameIn( )
	

	// TODO alles auch auf JOINS prüfen

	/**
	 * The method RemoveEscapes( ) returns the column or table name without escape characters ( ", ', ', ` )
	 *
	 * Example:
	 *
	 * @param string $name the name of the table or column
	 *
	 * @return string the column or table name without escape characters ( ", ', ', ` )
	 * 
	 */
	 
	static public function RemoveEscapes( $name ) {

	    // alle Anführungszeichen entfernen, wenn sie am Anfang und am Ende auftauchen

	    $name = trim( $name );

	    $chr_start = substr( $name, 0, 1 );

	    if ( $chr_start == '"' ) {

		if ( substr( $name, -1, 1 ) == '"' ) {

		    $name = substr( $name, 1, strlen( $name ) - 2  );

		}

	    } elseif ( $chr_start == "'" ) {

		if ( substr( $name, -1, 1 ) == "'" ) {

		    $name = substr( $name, 1, strlen( $name ) - 2  );

		}


	    } elseif ( $chr_start == '`' ) {


		if ( substr( $name, -1, 1 ) == '`' ) {

		    $name = substr( $name, 1, strlen( $name ) - 2  );

		}


	    }

/*
	    if ( ( substr( $name, 0, 1 ) == '"' ) || ( substr( $name, 0, 1 ) == "'" ) ) {
		if ( ( substr( $name, strlen( $name ) - 1, 1 ) == '"' ) || ( substr( $name, strlen( $name) -1 , 1 ) == "'" ) ) {

		    $name = substr( $name, 1 );
		    $name = substr( $name, 0, strlen( $name) - 1 );

		}
	    }
*/

	    return trim( $name );

	}	// function RemoveEscapes( )
	
	/**
	 * The method IsEscaped( ) returns true, if the column or table name is surrounded by escape characters ( ", ', ', ` )
	 *
	 * Example:
	 *
	 * @param string $name the name of the table or column
	 *
	 * @return bool true, if the column or table name is surrounded by escape characters ( ", ', ', ` )
	 * 
	 */
	
	

	static public function IsEscaped( $name ) {

	    // sind Anführungszeichen vorhanden, die am Anfang und am Ende auftauchen?

	    $name = trim( $name );

	    $chr_start = substr( $name, 0, 1 );

	    if ( $chr_start == '"' ) {

		return ( substr( $name, -1, 1 ) == '"' );

	    } elseif ( $chr_start == "'" ) {

		return ( substr( $name, -1, 1 ) == "'" );


	    } elseif ( $chr_start == '`' ) {

		return ( substr( $name, -1, 1 ) == '`' );

	    }

/*
	    if ( ( substr( $name, 0, 1 ) == '"' ) || ( substr( $name, 0, 1 ) == "'" ) ) {
		if ( ( substr( $name, strlen( $name ) - 1, 1 ) == '"' ) || ( substr( $name, strlen( $name) -1 , 1 ) == "'" ) ) {

		    return true;
		}
	    }
*/

	    return false;

	}	// function IsEscaped( )
	
	/**
	 * The method IsSubqueryStatement( ) returns true, if the query $statement is a subquery ( surrounded by ( and ) and a select statement )
	 *
	 * Example:
	 *
	 * @param string $statement  the query
	 *
	 * @return bool true, if the query $statement is a subquery ( surrounded by ( and ) and a select statement )
	 * 
	 */
	

	static public function IsSubqueryStatement( $statement ) {

	    // handelt es sich beim Statement um eine INLINE-Abfrage
	    
	    return self::IsSubquerySQL( $statement );
/*
	    $statement = trim( $statement );

	    if ( substr( $statement, 0, 1 ) == '(' ) {

		$statement = trim( substr( $statement, 1 ) );

		return ( stripos( $statement, 'SELECT' ) === 0 );
		

	    }
	    

	    return false;
*/	    

	}	// function IsSubqueryStatement( )
	
	/**
	 * The method CombineFieldName( ) constructs a fully qualified field name out of schema, table and column name  surrounded by ( and ) and a select statement )
	 *
	 * If $field_name is surrounded by functions then the functions will be applied on the fully qualified field name
	 * $schema_name, $table_name and $field_name must not be empty strings.
	 *
	 * Example:
	 *
	 * @param string $schema_name  the name of the schema
	 * @param string $table_name the name of the table
	 * @param string $field_name the field name
	 *
	 * @return string a fully qualified field name out of schema, table and column name  surrounded by ( and ) and a select statement )
	 * 
	 */	

    static public function CombineFieldName( $schema_name, $table_name, $field_name ) {

        $ret = '';

        if ( ! strlen( $schema_name ) ) { die( "\n CombineFieldName() mit leerem schema_name" ); }
        elseif ( ! strlen( $table_name ) ) { die( "\n CombineFieldName() mit leerem table_name" ); }
        elseif ( ! strlen( $field_name ) ) { die( "\n CombineFieldName() mit leerem field_name" ); }

        if ( strpos( $schema_name, '.' ) !== false ) { throw new \Exception("\n Fehler: cSQL::CombineFieldName() mit schema_name = '$schema_name'"); }
        elseif ( strpos( $table_name, '.' ) !== false ) { throw new \Exception("\n Fehler: cSQL::CombineFieldName() mit table_name = '$table_name'"); }
        elseif ( strpos( $field_name, '.' ) !== false ) { throw new \Exception("\n Fehler: cSQL::CombineFieldName() mit field_name = '$field_name'"); }

        $vgl = self::RemoveFunctions( $field_name );

        $schema_name = trim( $schema_name );
        $table_name = trim( $table_name );
        $field_name = trim( $field_name );

        if ( trim( $field_name ) == trim( $vgl )  ) {

            if ( strlen( $schema_name ) ) $ret .= $schema_name  .  '.';
            if ( strlen( $table_name ) ) $ret .=  $table_name .  '.'; else $ret = '';
            $ret .= $field_name ;

        } else {

            // wir haben eine oder mehrere Funktionen im Feldnamen

            if ( strlen( $schema_name ) ) $replace .= $schema_name .  '.';
            if ( strlen( $table_name ) ) $replace .=  $table_name  .  '.'; else $replace = '';
            $replace .= trim( $vgl );

            $ret = str_replace( $vgl, $replace, $field_name );

            // echo "<br>CombineFieldName liefert '$ret'";

        }

        if ( strpos( $ret, '..' ) !== false ) { throw new \ Exception("\n Fehler: cSQL::CombineFieldName() mit ret = '$ret'"); }

        return $ret;

    }	// function CombineFieldName( )

    
	/**
	 * The method RemoveAll( ) removes all aliases, functions and escapes from $field_name_full
	 *
	 *
	 * Example:
	 *
	 * @param string $field_name_full the field name 
	 *
	 * @return string the field name or table name without aliases, functions and escapes
	 * 
	 */	
    
    
      static public function RemoveAll( $field_name_full ) {

        $field_name = self::RemoveAlias( $field_name_full );
        $field_name = self::RemoveFunctions( $field_name );
        $field_name = self::RemoveEscapes( $field_name );

        return $field_name;

      }	// function RemoveAll( )
      
	/**
	 * The method IsValidFieldName( ) returns true, if $column_name is a valid field name
	 *
	 *
	 * Example:
	 *
	 * @param string $column_name the field name 
	 * @param bool $allow_points whether points are allowed in $column_name. $allow_points defaults to true.
	 *
	 * @return bool true, if $column_name is a valid field name
	 * 
	 */	
      

    static public function IsValidFieldName( $column_name, $allow_points = true ) {

	//
	// Der Qualifizierungspunkt wir mit berücksichtigt
	//

	$first = '_abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$next  = '_0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

	if ( $allow_points ) {

	      $next .= '.';

	}

	$column_name = trim( $column_name );

	if ( ! strlen( $column_name ) ) return false;

	for ( $i = 0; $i < strlen( $column_name); $i++ ) {

	    $chr = substr( $column_name, $i, 1 );

	    if ( $i > 0 ) {

		if ( strpos( $next, $chr) === false ) {

		    return false;

		}

	    } else {

		if ( strpos( $first, $chr) === false ) {

		    return false;

		}


	    }

	}

	return true;


    }	// function IsValidFieldName( )
    
	/**
	 * The method IsQualified( ) returns true, if $field_name is qualified and contains a table name and perhaps a schema name
	 *
	 *
	 * Example:
	 *
	 * @param string $field_name the field name 
	 *
	 * @return bool true, if $field_name is qualified and contains a table name and perhaps a schema name
	 * 
	 */	
    

    static public function IsQualified( $field_name ) {

        return ( strpos( $field_name, '.' ) !== false );
        
    }  // function IsQualified( )
    
	/**
     *
	 * The method SplitQualifiedFieldname( ) splits a qualified field name $org_field_name into its parts
	 *
	 * Functions get lost in this process
	 *
	 *
	 * Example:
	 *
	 * @param string $org_field_name the qualified field name 
	 * @param string $schema_name the name of the schema mentioned in $org_field_name
	 * @param string $table_name the name of the table mentioned in $org_field_name
	 * @param string $field_name the field name mentioned in $org_field_name
	 * @param string $alias the alias mentioned in $org_field_name
	 *
	 */	
    

    static public function SplitQualifiedFieldname( $org_fieldname, &$schema_name, &$table_name, &$field_name, &$alias ) {

	//
	// splittet, ohne zu versuchen, den Tabellennamen zu erraten
	// Funktionen usw gehen verloren
	//

    // 	echo '<br> starte SplitQualifiedFieldname mit ' . $org_fieldname;

        $schema_name = '';
        $table_name = '';
        $field_name = '';
        $alias = '';

        $org_fieldname = trim( self::RemoveFunctions( $org_fieldname ) );
        if ( substr( $org_fieldname, 0, 1 ) == '(' ) return;	// Subquery erhalten

        $org_fieldname = trim( self::RemoveEscapes( trim( $org_fieldname ) ) );

        if ( substr( trim( $org_fieldname ), 0, 1 ) == '(' ) {

            $field_name = trim ($org_fieldname );
            $table_name = '';
            $schema_name = '';

            return;

        }

        if ( ( $pos = strrpos( $org_fieldname, '.' ) ) !== false ) {

            $table_name = trim( substr( $org_fieldname, 0, $pos  ) );
            $field_name = trim( substr( $org_fieldname, $pos + 1 ) );

            if ( $pos = strrpos( $table_name, '.' ) ) {

            $schema_name = trim( substr( $table_name, 0, $pos  ) );
            $table_name = trim( substr( $table_name, $pos + 1 ) );

            }

        } else {

            $field_name = trim( self::RemoveAlias( $org_fieldname ) );
            $table_name = '';

        }

        $alias = self::GetAliasFromTablename( $org_fieldname );

        $field_name = trim( self::RemoveAlias( $field_name ) );

    // 	echo '<br>' . $field_name . ' wird nach RemoveAlias zu ' . $rest;

        $field_name = self::RemoveEscapes( $field_name );
        $schema_name = self::RemoveEscapes( $schema_name );
        $table_name = self::RemoveEscapes( $table_name );



    }	// function SplitQualifiedFieldname( )
    
	/**
     *
	 * The method RemoveQualification( ) removes schema name and table name from the fully qualified field name $org_field_name.
	 *
	 * Functions get lost in this process
	 *
	 *
	 * Example:
	 *
	 * @param string $org_field_name the qualified field name 
	 * @param bool $remove_alias if $remove_alias is true, then the alias will be removed, too. Defaults to true.
	 *
	 * @return string $org_field_name without schema name and table name.
	 *
	 */	    

      static public function RemoveQualification( $org_field_name, $remove_alias = true ) {

            // entffernt Datenbanknamen und Tabellennamen und evt den Alias

            self::SplitQualifiedFieldname( $org_field_name, $schema_name, $table_name, $field_name, $alias );

            $ret = $field_name;

            if ( ! $remove_alias ) if ( strlen( $alias ) ) $ret .= ' as ' . $alias;

            return $ret;

      }	// function RemoveQualification( )
      
      
	/**
     *
	 * The method IsJoin( ) returns true, if the table name $table_name is a join.
	 *
	 *
	 * Example:
	 *
	 * @param string $table_name the table name to analyze
	 *
	 * @return bool true, if the table name $table_name is a join.
	 *
	 */	    

      static public function IsJoin( $table_name ) {

	  $a_patterns = array(
	      "/[+[:blank:]][jJ][oO][iI][nN][+[:blank:]]/",
	      "/[+[:blank:]][sS][tT][rR][aA][iI][gG][hH][tT][_][jJ][oO][iI][nN][+[:blank:]]/"
	  );

	  foreach( $a_patterns as $pattern ) {

	      if ( preg_match( $pattern, $table_name ) === 1 ) {

		  return true;

	      }

	  }

	  return false;


      }	// function IsJoin( )
      
      
	/**
     *
	 * The method FormatStatement( ) reformats the statement $sql
	 *
	 *
	 * Example:
	 *
	 * @param string $sql the query string
	 *
	 * @return string the reformatted query string
	 *
	 */	    
      

    static public function FormatStatement( $sql ) {

	 $sql = preg_replace( '/[+[:blank:]][fF][rR][oO][mM][+[:blank:]]/', "\n from \n", $sql );
	 $sql = preg_replace( '/[+[:blank:]][wW][hH][eE][rR][eE][+[:blank:]]/', "\n where \n", $sql );
	 $sql = preg_replace( '/[+[:blank:]][gG][rR][oO][uU][pP][+[:blank:]][bB][yY][+[:blank:]\n\r]/', "\n group by \n", $sql );
	 $sql = preg_replace( '/[+[:blank:]][hH][aA][vV][iI][nN][gG][+[:blank:]]/', "\n having \n", $sql );
	 $sql = preg_replace( '/[sS][eE][lL][eE][cC][tT][+[:blank:]]/', "\n select \n", $sql );


	 return $sql;

    }	// function FormatStatement( )
    
	/**
     *
	 * The method GetRealFieldname( ) returns the real field name without alias and with the table and schema name - if mentioned. 
	 * $field_name must not contain joins
	 * functions will be removed from the field name
	 *
	 * Example:
	 *
	 * @param string $field_name the field name to analyze
	 * @param string $table_definition the table definition as meant in a select statement ( ie: "table1, table2 as t2" )
	 *
	 * @return string the real field name
	 *
	 *
	 * @see GetAliasOrRealFieldname( )
	 *
	 */	        


	static public function GetRealFieldname( $field_name, $table_definition ) {

	    // der echte Feldname ohne ALIAS und mit dem echten Tabellennamen
	    // TODO: Funktionen sollen erhalten bleiben

	    
      $table_definition = trim( $table_definition );
      if ( !strlen( $table_definition ) ) throw new \Exception( "\n GetRealFieldname( ) without table definition!" );
      
	  $ret = trim( self::RemoveAlias( self::RemoveEscapes( $field_name ) ) );	  

	  $is_schema = false;
	  $schema_name = '';
	  $escaped = false;


	  if ( $pos = strpos( $ret, '.' ) !== false ) {

	      // Tabellenname und ggf Schemaname benannt

	      if ( $pos_schema = strpos( $ret, $pos + 1 ) !== false ) {

            // Schemanamen isolieren


            $is_schema = true;

            $schema_name = substr( $ret, 0, $pos_schema - 1 );
            $ret = substr( $ret, $pos_schema + 1 );

            $pos = strpos( $ret, '.' );

	      }

	       if ( ( $pos = strpos( $ret, '.' ) ) !== false ) {
            // den Alias in den Tabellennamen  ersetzen durch den richtigen Tabellennamen

                $tables = explode( ',', $table_definition );
                $tst_name = trim( substr( $ret, 0, $pos  ) );

                for ( $i = 0; $i < count( $tables ); $i++ ) {	// TODO auch Joins entfernen


                    $table = $tables[ $i ];
                    $alias = self::GetAliasFromTablename( $table );
                    $wo_alias = self::RemoveAlias( $table );

                    if ( strlen( $alias ) && ( $tst_name == $alias ) ) {

                        $ret = $wo_alias . substr( $ret, $pos );

                    }


                }
	      }
	  }

	  $ret = self::RemoveFunctions( $ret );

	  if ( $is_schema ) $ret = $schema_name . '.' . $ret;


	  return $ret;

	}	// function GetRealFieldname( )


	/**
     *
	 * The method GetAliasOrRealFieldname( ) returns the real field name or the alias with the table and schema name - if mentioned. 
	 * $field_name must not contain joins
	 * functions will be removed from the field name
	 *
	 * Example:
	 *
	 * @param string $field_name the field name to analyze
	 * @param string $table_definition the table definition as meant in a select statement ( ie: "table1, table2 as t2" )
	 *
	 * @return string the real field name or the alias
	 *
	 *
	 * @see GetAliasOrRealFieldname( )
	 *
	 */	 	
	
	static public function GetAliasOrRealFieldname( $field_name, $table_definition ) {	// TODO ALIAS ist auch ohne AS möglich
	
      $table_definition = trim( $table_definition );
      if ( !strlen( $table_definition ) ) throw new \Exception( "\n GetAliasOrRealFieldname( ) without table definition!" );
	


	    $real_name = self::GetRealFieldname( $field_name, $table_definition );
	    $alias = self::GetAliasFromTablename( $field_name );

	    if ( ( $alias !== $real_name ) && ( strlen( $alias ) ) ) {
		return trim( $alias );
	    }

	    return trim( $real_name );



	}	// function GetAliasOrRealFieldname( )

}		// class  cSQL
?>

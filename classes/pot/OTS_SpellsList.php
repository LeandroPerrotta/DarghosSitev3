<?php

/**
 * @package POT
 * @version 0.2.0+SVN
 * @since 0.1.0
 * @author Wrzasq <wrzasq@gmail.com>
 * @copyright 2007 - 2009 (C) by Wrzasq
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt GNU Lesser General Public License, Version 3
 */

/**
 * Wrapper for spells list.
 * 
 * <p>
 * Note: Unlike other lists classes this one doesn't implement ArrayAccess interface because it contains three kinds of spells grouped into pararell arrays.
 * </p>
 * 
 * @package POT
 * @version 0.2.0+SVN
 * @since 0.1.0
 * @property-read array $runesList List of rune spells.
 * @property-read array $instantsList List of instant spells.
 * @property-read array $conjuresList List of conjure spells.
 * @tutorial POT/data_directory.pkg#spells
 */
class OTS_SpellsList implements IteratorAggregate, Countable
{
/**
 * Rune spell.
 * 
 * @version 0.1.0
 * @since 0.1.0
 */
    const SPELL_RUNE = 0;
/**
 * Instant spell.
 * 
 * @version 0.1.0
 * @since 0.1.0
 */
    const SPELL_INSTANT = 1;
/**
 * Conjure spell.
 * 
 * @version 0.1.0
 * @since 0.1.0
 */
    const SPELL_CONJURE = 2;

/**
 * Rune spells.
 * 
 * @version 0.1.0
 * @since 0.1.0
 * @var array
 */
    private $runes = array();

/**
 * Instant spells.
 * 
 * @version 0.1.0
 * @since 0.1.0
 * @var array
 */
    private $instants = array();

/**
 * Conjure spells.
 * 
 * @version 0.1.0
 * @since 0.1.0
 * @var array
 */
    private $conjures = array();

/**
 * Magic PHP5 method.
 * 
 * <p>
 * Allows object importing from {@link http://www.php.net/manual/en/function.var-export.php var_export()}.
 * </p>
 * 
 * @version 0.2.0+SVN
 * @since 0.1.0
 * @param array $properties List of object properties.
 */
    public function __set_state(array $properties)
    {
        $object = new self();

        // loads properties
        foreach($properties as $name => $value)
        {
            $object->$name = $value;
        }

        return $object;
    }

/**
 * Loads spells list.
 * 
 * @version 0.1.0
 * @since 0.1.0
 * @param string $file Spells file name.
 * @throws DOMException On DOM operation error.
 */
    public function __construct($file)
    {
        // loads DOM document
        $spells = new DOMDocument();
        $spells->load($file);

        // loads runes
        foreach( $spells->getElementsByTagName('rune') as $rune)
        {
            $this->runes[ $rune->getAttribute('name') ] = new OTS_Spell(self::SPELL_RUNE, $rune);
        }

        // loads instants
        foreach( $spells->getElementsByTagName('instant') as $instant)
        {
            $this->instants[ $instant->getAttribute('name') ] = new OTS_Spell(self::SPELL_INSTANT, $instant);
        }

        // loads conjures
        foreach( $spells->getElementsByTagName('conjure') as $conjure)
        {
            $this->conjures[ $conjure->getAttribute('name') ] = new OTS_Spell(self::SPELL_CONJURE, $conjure);
        }
    }

/**
 * Returns list of runes.
 * 
 * @version 0.1.0
 * @since 0.1.0
 * @return array List of rune names.
 */
    public function getRunesList()
    {
        return array_keys($this->runes);
    }

/**
 * Checks if rune exists.
 * 
 * @version 0.1.3
 * @since 0.1.3
 * @param string $name Rune name.
 * @return bool If rune is set then true.
 */
    public function hasRune($name)
    {
        return isset($this->runes[$name]);
    }

/**
 * Returns given rune spell.
 * 
 * @version 0.1.3
 * @since 0.1.0
 * @param string $name Rune name.
 * @return OTS_Spell Rune spell wrapper.
 * @throws OutOfBoundsException If rune does not exist.
 */
    public function getRune($name)
    {
        if( isset($this->runes[$name]) )
        {
            return $this->runes[$name];
        }

        throw new OutOfBoundsException();
    }

/**
 * Returns list of instants.
 * 
 * @version 0.1.0
 * @since 0.1.0
 * @return array List of instant spells names.
 */
    public function getInstantsList()
    {
        return array_keys($this->instants);
    }

/**
 * Checks if instant exists.
 * 
 * @version 0.1.3
 * @since 0.1.3
 * @param string $name Instant name.
 * @return bool If instant is set then true.
 */
    public function hasInstant($name)
    {
        return isset($this->instants[$name]);
    }

/**
 * Returns given instant spell.
 * 
 * @version 0.1.3
 * @since 0.1.0
 * @param string $name Spell name.
 * @return OTS_Spell Instant spell wrapper.
 * @throws OutOfBoundsException If instant does not exist.
 */
    public function getInstant($name)
    {
        if( isset($this->instants[$name]) )
        {
            return $this->instants[$name];
        }

        throw new OutOfBoundsException();
    }

/**
 * Returns list of conjure spells.
 * 
 * @version 0.1.0
 * @since 0.1.0
 * @return array List of conjure spells names.
 */
    public function getConjuresList()
    {
        return array_keys($this->conjures);
    }

/**
 * Checks if conjure exists.
 * 
 * @version 0.1.3
 * @since 0.1.3
 * @param string $name Conjure name.
 * @return bool If conjure is set then true.
 */
    public function hasConjure($name)
    {
        return isset($this->conjures[$name]);
    }

/**
 * Returns given conjure spell.
 * 
 * @version 0.1.3
 * @since 0.1.0
 * @param string $name Spell name.
 * @return OTS_Spell Conjure spell wrapper.
 * @throws OutOfBoundsException If conjure does not exist.
 */
    public function getConjure($name)
    {
        if( isset($this->conjures[$name]) )
        {
            return $this->conjures[$name];
        }

        throw new OutOfBoundsException();
    }

/**
 * Magic PHP5 method.
 * 
 * @version 0.1.0
 * @since 0.1.0
 * @param string $name Property name.
 * @return mixed Property value.
 * @throws OutOfBoundsException For non-supported properties.
 */
    public function __get($name)
    {
        switch($name)
        {
            case 'runesList':
                return $this->getRunesList();

            case 'instantsList':
                return $this->getInstantsList();

            case 'conjuresList':
                return $this->getConjuresList();

            default:
                throw new OutOfBoundsException();
        }
    }

/**
 * Returns string representation of object.
 * 
 * <p>
 * If any display driver is currently loaded then it uses it's method.
 * </p>
 * 
 * @version 0.2.0+SVN
 * @since 0.1.3
 * @return string String representation of object.
 */
    public function __toString()
    {
        // checks if display driver is loaded
        if( POT::isDataDisplayDriverLoaded() )
        {
            return POT::getDataDisplayDriver()->displaySpellsList($this);
        }

        return (string) $this->count();
    }

/**
 * Iterator for all spells.
 * 
 * <p>
 * Returned object will continousely iterate through all kind of spells.
 * </p>
 * 
 * @version 0.1.5
 * @since 0.1.5
 * @return AppendIterator Iterator for all spells.
 */
    public function getIterator()
    {
        $iterator = new AppendIterator();
        $iterator->append( new ArrayIterator($this->runes) );
        $iterator->append( new ArrayIterator($this->instants) );
        $iterator->append( new ArrayIterator($this->conjures) );
        return $iterator;
    }

/**
 * Number of all loaded spells.
 * 
 * @version 0.1.5
 * @since 0.1.5
 * @return int Amount of all spells.
 */
    public function count()
    {
        return count($this->runes) + count($this->instants) + count($this->conjures);
    }
}

?>

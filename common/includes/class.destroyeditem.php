<?php
/**
 * $Date$
 * $Revision$
 * $HeadURL$
 * @package EDK
 */

/**
 * @package EDK
 */
class DestroyedItem
{
	/** @var Item */
	public $item_;
	/** @var integer */
	public $quantity_;
	/** @var string|integer */
	public $location_;
	/** @var integer */
	public $locationID_;
	/** @var float */
	private $value;
        /** @var integer */
        private $singleton_;

	/**
	 * @param Item $item
	 * @param integer $quantity
	 * @param integer $singleton
         * * @param string|integer $location
	 * @param integer $locationID
	 */
	function __construct($item, $quantity, $singleton, $location, $locationID = 0)
	{
		$this->item_ = $item;
		$this->quantity_ = (int) $quantity;
                $this->singleton_ = (int) $singleton;
		$this->location_ = $location;
		$this->locationID_ = (int) $locationID;
	}

	/**
	 * @return Item
	 */
	function getItem()
	{
		return $this->item_;
	}

	/**
	 * @return integer
	 */
	function getQuantity()
	{
		if (!$this->quantity_) {
			$this->quantity_ = 1;
		}
		return $this->quantity_;
	}
	/**
	 * Return value formatted into millions or thousands.
	 *
	 * @return string
	 */
	function getFormattedValue()
	{
		if (!isset($this->value)) {
			$this->getValue();
		}

		if ($this->value > 0) {
			$value = $this->value * $this->getQuantity();
			// Value Manipulation for prettyness.
			if ($value > 1000000) {
				$formatted = round($value / 1000000, 2);
				$formatted = number_format($formatted, 2);
				$formatted = $formatted." M";
			} else if ($value > 1000) {
				$formatted = round($value / 1000, 2);

				$formatted = number_format($formatted, 2);
				$formatted = $formatted." K";
			} else {
				$formatted = number_format($value, 2);
				$formatted = $formatted." ISK";
			}
		} else {
			$formatted = "0 ISK";
		}
		return $formatted;
	}

	/**
	 * @return float
	 */
	function getValue()
	{
		if (isset($this->value)) {
			return $this->value;
		}
                // special treatment for BPCs for now
                if($this->item_->getAttribute('itt_cat') == 9 && $this->singleton_ == 2)
                {
                    return 0;
                }
		if ($this->item_->getAttribute('price')) {
			$this->value = (float) $this->item_->getAttribute('price');
			return $this->value;
		} else if ($this->item_->getAttribute('basePrice')) {
			$this->value = (float) $this->item_->getAttribute('basePrice');
			return $this->value;
		}

		$qry = DBFactory::getDBQuery();
		$qry->execute("SELECT basePrice, price
					FROM kb3_invtypes
					LEFT JOIN kb3_item_price ON kb3_invtypes.typeID=kb3_item_price.typeID
					WHERE kb3_invtypes.typeID='".$this->item_->getID()."'");
		if ($row = $qry->getRow()) {
			if ($row['price']) {
				$this->value = (float) $row['price'];
			} else {
				$this->value = (float) $row['basePrice'];
			}
		} else {
			$this->value = 0;
		}
		return $this->value;
	}

	/**
	 * @return integer
	 */
	function getLocationID()
	{
		if(!is_null($this->locationID_) && $this->locationID_ != InventoryFlag::$UNKNOWN) {
			return $this->locationID_;
		}
		if ($this->location_ || strlen($this->location_) == 0) {
			$this->locationID_ = (int) $this->item_->getSlot();
		} else { 
                        $InventoryFlag = InventoryFlag::getConvertedByName($this->location_);
			$this->locationID_ = $InventoryFlag->getID();
		}
		return $this->locationID_;
	}
        
        /**
         * @return integer
         */
        function getSingleton()
        {
            return $this->singleton_;
        }
}
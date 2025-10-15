<?php
declare(strict_types=1);

namespace Tmms\CmsElementPeriodRequestForm\Core\Content\PeriodRequestForm;

use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class PeriodRequestFormEntity extends Entity
{
    use EntityIdTrait;

    /**
     * @var string
     */
    protected $salutation;

    /**
     * @var string
     */
    protected $firstname;

    /**
     * @var string
     */
    protected $lastname;

    /**
     * @var string
     */
    protected $street;

    /**
     * @var string
     */
    protected $zipcode;

    /**
     * @var string
     */
    protected $city;

    /**
     * @var string
     */
    protected $country;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var string
     */
    protected $phone;

    /**
     * @var string
     */
    protected $comment;

    /**
     * @var string
     */
    protected $date;

    /**
     * @var string
     */
    protected $freeinputlabel;

    /**
     * @var string
     */
    protected $freeinput;

    /**
     * @var string
     */
    protected $freeinput2label;

    /**
     * @var string
     */
    protected $freeinput2;

    /**
     * @var string
     */
    protected $freeinput3label;

    /**
     * @var string
     */
    protected $freeinput3;

    /**
     * @var string
     */
    protected $freeinput4label;

    /**
     * @var string
     */
    protected $freeinput4;

    /**
     * @var string
     */
    protected $freeinput5label;

    /**
     * @var string
     */
    protected $freeinput5;

    /**
     * @var string
     */
    protected $freeinput6label;

    /**
     * @var string
     */
    protected $freeinput6;

    /**
     * @var string
     */
    protected $freeinput7label;

    /**
     * @var string
     */
    protected $freeinput7;

    /**
     * @var string
     */
    protected $freeinput8label;

    /**
     * @var string
     */
    protected $freeinput8;

    /**
     * @var string
     */
    protected $freeinput9label;

    /**
     * @var string
     */
    protected $freeinput9;

    /**
     * @var string
     */
    protected $freeinput10label;

    /**
     * @var string
     */
    protected $freeinput10;

    /**
     * @var string
     */
    protected $origin;

    /**
     * @var string
     */
    protected $originid;

    /**
     * @var string
     */
    protected $originname;

    /**
     * @var bool
     */
    protected $confirmed;

    /**
     * @var bool
     */
    protected $answered;

    public function getSalutation(): ?string
    {
        return $this->salutation;
    }

    public function setSalutation(string $salutation): void
    {
        $this->salutation = $salutation;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): void
    {
        $this->firstname = $firstname;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): void
    {
        $this->lastname = $lastname;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(string $street): void
    {
        $this->street = $street;
    }

    public function getZipcode(): ?string
    {
        return $this->zipcode;
    }

    public function setZipcode(string $zipcode): void
    {
        $this->zipcode = $zipcode;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): void
    {
        $this->city = $city;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): void
    {
        $this->country = $country;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): void
    {
        $this->phone = $phone;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(string $comment): void
    {
        $this->comment = $comment;
    }

    public function getDate(): ?string
    {
        return $this->date;
    }

    public function setDate(string $date): void
    {
        $this->date = $date;
    }

    public function getFreeinputlabel(): ?string
    {
        return $this->freeinputlabel;
    }

    public function setFreeinputlabel(string $freeinputlabel): void
    {
        $this->freeinputlabel = $freeinputlabel;
    }

    public function getFreeinput(): ?string
    {
        return $this->freeinput;
    }

    public function setFreeinput(string $freeinput): void
    {
        $this->freeinput = $freeinput;
    }

    public function getFreeinput2label(): ?string
    {
        return $this->freeinput2label;
    }

    public function setFreeinput2label(string $freeinput2label): void
    {
        $this->freeinput2label = $freeinput2label;
    }

    public function getFreeinput2(): ?string
    {
        return $this->freeinput2;
    }

    public function setFreeinput2(string $freeinput2): void
    {
        $this->freeinput2 = $freeinput2;
    }

    public function getFreeinput3label(): ?string
    {
        return $this->freeinput3label;
    }

    public function setFreeinput3label(string $freeinput3label): void
    {
        $this->freeinput3label = $freeinput3label;
    }

    public function getFreeinput3(): ?string
    {
        return $this->freeinput3;
    }

    public function setFreeinput3(string $freeinput3): void
    {
        $this->freeinput3 = $freeinput3;
    }

    public function getFreeinput4label(): ?string
    {
        return $this->freeinput4label;
    }

    public function setFreeinput4label(string $freeinput4label): void
    {
        $this->freeinput4label = $freeinput4label;
    }

    public function getFreeinput4(): ?string
    {
        return $this->freeinput4;
    }

    public function setFreeinput4(string $freeinput4): void
    {
        $this->freeinput4 = $freeinput4;
    }

    public function getFreeinput5label(): ?string
    {
        return $this->freeinput5label;
    }

    public function setFreeinput5label(string $freeinput5label): void
    {
        $this->freeinput5label = $freeinput5label;
    }

    public function getFreeinput5(): ?string
    {
        return $this->freeinput5;
    }

    public function setFreeinput5(string $freeinput5): void
    {
        $this->freeinput5 = $freeinput5;
    }

    public function getFreeinput6label(): ?string
    {
        return $this->freeinput6label;
    }

    public function setFreeinput6label(string $freeinput6label): void
    {
        $this->freeinput6label = $freeinput6label;
    }

    public function getFreeinput6(): ?string
    {
        return $this->freeinput6;
    }

    public function setFreeinput6(string $freeinput6): void
    {
        $this->freeinput6 = $freeinput6;
    }

    public function getFreeinput7label(): ?string
    {
        return $this->freeinput7label;
    }

    public function setFreeinput7label(string $freeinput7label): void
    {
        $this->freeinput7label = $freeinput7label;
    }

    public function getFreeinput7(): ?string
    {
        return $this->freeinput7;
    }

    public function setFreeinput7(string $freeinput7): void
    {
        $this->freeinput7 = $freeinput7;
    }

    public function getFreeinput8label(): ?string
    {
        return $this->freeinput8label;
    }

    public function setFreeinput8label(string $freeinput8label): void
    {
        $this->freeinput8label = $freeinput8label;
    }

    public function getFreeinput8(): ?string
    {
        return $this->freeinput8;
    }

    public function setFreeinput8(string $freeinput8): void
    {
        $this->freeinput8 = $freeinput8;
    }

    public function getFreeinput9label(): ?string
    {
        return $this->freeinput9label;
    }

    public function setFreeinput9label(string $freeinput9label): void
    {
        $this->freeinput9label = $freeinput9label;
    }

    public function getFreeinput9(): ?string
    {
        return $this->freeinput9;
    }

    public function setFreeinput9(string $freeinput9): void
    {
        $this->freeinput9 = $freeinput9;
    }

    public function getFreeinput10label(): ?string
    {
        return $this->freeinput10label;
    }

    public function setFreeinput10label(string $freeinput10label): void
    {
        $this->freeinput10label = $freeinput10label;
    }

    public function getFreeinput10(): ?string
    {
        return $this->freeinput10;
    }

    public function setFreeinput10(string $freeinput10): void
    {
        $this->freeinput10 = $freeinput10;
    }

    public function setOrigin(string $origin): void
    {
        $this->origin = $origin;
    }

    public function getOrigin(): ?string
    {
        return $this->origin;
    }

    public function setOriginid(string $originid): void
    {
        $this->originid = $originid;
    }

    public function getOriginid(): ?string
    {
        return $this->originid;
    }

    public function setOriginname(string $originname): void
    {
        $this->originname = $originname;
    }

    public function getOriginname(): ?string
    {
        return $this->originname;
    }

    public function getConfirmed(): bool
    {
        return $this->confirmed;
    }

    public function setConfirmed(bool $confirmed): void
    {
        $this->confirmed = $confirmed;
    }

    public function getAnswered(): bool
    {
        return $this->answered;
    }

    public function setAnswered(bool $answered): void
    {
        $this->answered = $answered;
    }
}

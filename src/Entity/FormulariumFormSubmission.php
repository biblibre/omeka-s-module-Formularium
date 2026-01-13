<?php

namespace Formularium\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Omeka\Entity\AbstractEntity;
use Omeka\Entity\User;
use Omeka\Entity\Site;
use Omeka\Entity\SitePage;
use Omeka\Entity\SitePageBlock;

/**
 * @Entity
 */
class FormulariumFormSubmission extends AbstractEntity
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue
     */
    protected $id;

    /**
     * @ManyToOne(targetEntity="Formularium\Entity\FormulariumForm")
     * @JoinColumn(nullable=false, onDelete="CASCADE")
     */
    protected FormulariumForm $form;

    /**
     * @ManyToOne(targetEntity="Omeka\Entity\Site")
     * @JoinColumn(onDelete="SET NULL")
     */
    protected ?Site $site;

    /**
     * @ManyToOne(targetEntity="Omeka\Entity\SitePage")
     * @JoinColumn(onDelete="SET NULL")
     */
    protected ?SitePage $sitePage;

    /**
     * @ManyToOne(targetEntity="Omeka\Entity\SitePageBlock")
     * @JoinColumn(onDelete="SET NULL")
     */
    protected ?SitePageBlock $sitePageBlock;

    /**
     * @ManyToOne(targetEntity="Omeka\Entity\User")
     * @JoinColumn(onDelete="SET NULL")
     */
    protected ?User $submitter;

    /**
     * @ManyToOne(targetEntity="Omeka\Entity\User")
     * @JoinColumn(onDelete="SET NULL")
     */
    protected ?User $handler;

    /**
     * @Column(type="datetime")
     */
    protected DateTime $submitted;

    /**
     * @Column(type="datetime", nullable=true)
     */
    protected ?DateTime $handled;

    /**
     * @Column(type="json")
     */
    protected array $data;

    /**
     * @OneToMany(
     *     targetEntity="FormulariumFormSubmissionFile",
     *     mappedBy="formSubmission",
     *     orphanRemoval=true,
     *     cascade={"persist", "remove", "detach"},
     *     indexBy="id"
     * )
     */
    protected $files;

    public function __construct()
    {
        $this->files = new ArrayCollection;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getForm(): FormulariumForm
    {
        return $this->form;
    }

    public function setForm(FormulariumForm $form): void
    {
        $this->form = $form;
    }

    public function getSite(): ?Site
    {
        return $this->site;
    }

    public function setSite(Site $site): void
    {
        $this->site = $site;
    }

    public function getSitePage(): ?SitePage
    {
        return $this->sitePage;
    }

    public function setSitePage(SitePage $sitePage): void
    {
        $this->sitePage = $sitePage;
    }

    public function getSitePageBlock(): ?SitePageBlock
    {
        return $this->sitePageBlock;
    }

    public function setSitePageBlock(SitePageBlock $sitePageBlock): void
    {
        $this->sitePageBlock = $sitePageBlock;
    }

    public function getSubmitter(): ?User
    {
        return $this->submitter;
    }

    public function setSubmitter(?User $submitter): void
    {
        $this->submitter = $submitter;
    }

    public function getHandler(): ?User
    {
        return $this->handler;
    }

    public function setHandler(?User $handler): void
    {
        $this->handler = $handler;
    }

    public function getSubmitted(): DateTime
    {
        return $this->submitted;
    }

    public function setSubmitted(DateTime $submitted): void
    {
        $this->submitted = $submitted;
    }

    public function getHandled(): ?DateTime
    {
        return $this->handled;
    }

    public function setHandled(?DateTime $handled): void
    {
        $this->handled = $handled;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function setData(array $data): void
    {
        $this->data = $data;
    }

    public function getFiles()
    {
        return $this->files;
    }
}

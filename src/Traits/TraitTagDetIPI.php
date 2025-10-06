<?php

namespace NFePHP\NFe\Traits;

use NFePHP\Common\DOMImproved as Dom;
use stdClass;
use DOMElement;
use DOMException;

/**
 * @property  Dom $dom
 * @property stdClass $stdTot
 * @property array $aIPI
 * @method equilizeParameters($std, $possible)
 * @method conditionalNumberFormatting($value, $decimal = 2)
 */
trait TraitTagDetIPI
{

    public function tagIPI(stdClass $std): DOMElement
    {
        $possible = [
            'item',
            'clEnq',
            'CNPJProd',
            'cSelo',
            'qSelo',
            'cEnq',
            'CST',
            'vIPI',
            'vBC',
            'pIPI',
            'qUnid',
            'vUnid'
        ];
        $std = $this->equilizeParameters($std, $possible);
        $ipi = $this->dom->createElement('IPI');
        $this->dom->addChild(
            $ipi,
            "clEnq",
            $std->clEnq,
            false,
            "[item $std->item] Classe de enquadramento do IPI para Cigarros e Bebidas"
        );
        $this->dom->addChild(
            $ipi,
            "CNPJProd",
            $std->CNPJProd,
            false,
            "[item $std->item] CNPJ do produtor da mercadoria, quando diferente do emitente. "
            . "Somente para os casos de exportação direta ou indireta."
        );
        $this->dom->addChild(
            $ipi,
            "cSelo",
            $std->cSelo,
            false,
            "[item $std->item] Código do selo de controle IPI"
        );
        $this->dom->addChild(
            $ipi,
            "qSelo",
            $std->qSelo,
            false,
            "[item $std->item] Quantidade de selo de controle"
        );
        $this->dom->addChild(
            $ipi,
            "cEnq",
            $std->cEnq,
            true,
            "[item $std->item] Código de Enquadramento Legal do IPI"
        );
        if ($std->CST == '00' || $std->CST == '49' || $std->CST == '50' || $std->CST == '99') {
            //totalizador
            $this->stdTot->vIPI += (float) $std->vIPI;
            $ipiTrib = $this->dom->createElement('IPITrib');
            $this->dom->addChild(
                $ipiTrib,
                "CST",
                $std->CST,
                true,
                "[item $std->item] Código da situação tributária do IPI"
            );
            $this->dom->addChild(
                $ipiTrib,
                "vBC",
                $this->conditionalNumberFormatting($std->vBC),
                false,
                "[item $std->item] Valor da BC do IPI"
            );
            $this->dom->addChild(
                $ipiTrib,
                "pIPI",
                $this->conditionalNumberFormatting($std->pIPI, 4),
                false,
                "[item $std->item] Alíquota do IPI"
            );
            $this->dom->addChild(
                $ipiTrib,
                "qUnid",
                $this->conditionalNumberFormatting($std->qUnid, 4),
                false,
                "[item $std->item] Quantidade total na unidade padrão para tributação (somente para os "
                . "produtos tributados por unidade)"
            );
            $this->dom->addChild(
                $ipiTrib,
                "vUnid",
                $this->conditionalNumberFormatting($std->vUnid, 4),
                false,
                "[item $std->item] Valor por Unidade Tributável"
            );
            $this->dom->addChild(
                $ipiTrib,
                "vIPI",
                $this->conditionalNumberFormatting($std->vIPI),
                true,
                "[item $std->item] Valor do IPI"
            );
            $ipi->appendChild($ipiTrib);
        } else {
            $ipINT = $this->dom->createElement('IPINT');
            $this->dom->addChild(
                $ipINT,
                "CST",
                $std->CST,
                true,
                "[item $std->item] Código da situação tributária do IPINT"
            );
            $ipi->appendChild($ipINT);
        }
        $this->aIPI[$std->item] = $ipi;
        return $ipi;
    }
    /**
     * Grupo IPI O01 pai M01
     * tag NFe/infNFe/det[]/imposto/IPI (opcional)
     * @param stdClass $std
     * @return DOMElement
     * @throws DOMException
     */
    public function tagIPI2(stdClass $std): DOMElement
    {
        $possible = [
            'item',
            'CNPJProd',
            'cSelo',
            'qSelo',
            'cEnq',
            'CST',
            'vIPI',
            'vBC',
            'pIPI',
            'qUnid',
            'vUnid'
        ];
        $std = $this->equilizeParameters($std, $possible);
        $identificador = "O01 <IPI> Item: $std->item -";
        $ipi = $this->dom->createElement('IPI');
        $this->dom->addChild(
            $ipi,
            "CNPJProd",
            $std->CNPJProd,
            false,
            "$identificador CNPJ do produtor da mercadoria, quando diferente do emitente. "
            . "Somente para os casos de exportação direta ou indireta. (CNPJProd)"
        );
        $this->dom->addChild(
            $ipi,
            "cSelo",
            $std->cSelo,
            false,
            "$identificador Código do selo de controle IPI (cSelo)"
        );
        $this->dom->addChild(
            $ipi,
            "qSelo",
            $std->qSelo,
            false,
            "$identificador Quantidade de selo de controle (qSelo)"
        );
        $this->dom->addChild(
            $ipi,
            "cEnq",
            $std->cEnq,
            true,
            "$identificador Código de Enquadramento Legal do IPI (cEnq)"
        );
        if ($std->CST == '00' || $std->CST == '49' || $std->CST == '50' || $std->CST == '99') {
            //totalizador
            $this->stdTot->vIPI += (float) $std->vIPI;
            $ipiTrib = $this->dom->createElement('IPITrib');
            $this->dom->addChild(
                $ipiTrib,
                "CST",
                $std->CST,
                true,
                "$identificador Código da situação tributária do IPI (CST)"
            );
            if (!empty($std->vBC)) {
                $this->dom->addChild(
                    $ipiTrib,
                    "vBC",
                    $this->conditionalNumberFormatting($std->vBC),
                    false,
                    "$identificador Valor da BC do IPI (vBC)"
                );
                $this->dom->addChild(
                    $ipiTrib,
                    "pIPI",
                    $this->conditionalNumberFormatting($std->pIPI ?? 0, 4),
                    false,
                    "$identificador Alíquota do IPI (pIPI)"
                );
            } else {
                $this->dom->addChild(
                    $ipiTrib,
                    "qUnid",
                    $this->conditionalNumberFormatting($std->qUnid, 4),
                    false,
                    "$identificador Quantidade total na unidade padrão para tributação (somente para os "
                    . "produtos tributados por unidade) (qUnid)"
                );
                $this->dom->addChild(
                    $ipiTrib,
                    "vUnid",
                    $this->conditionalNumberFormatting($std->vUnid, 4),
                    false,
                    "$identificador Valor por Unidade Tributável (vUnid)"
                );
            }
            $this->dom->addChild(
                $ipiTrib,
                "vIPI",
                $this->conditionalNumberFormatting($std->vIPI),
                true,
                "$identificador Valor do IPI (vIPI)"
            );
            $ipi->appendChild($ipiTrib);
        } else {
            $ipINT = $this->dom->createElement('IPINT');
            $this->dom->addChild(
                $ipINT,
                "CST",
                $std->CST,
                true,
                "$identificador Código da situação tributária do IPINT (CST)"
            );
            $ipi->appendChild($ipINT);
        }
        $this->aIPI[$std->item] = $ipi;
        return $ipi;
    }
}

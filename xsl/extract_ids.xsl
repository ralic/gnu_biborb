<?xml version="1.0" encoding="UTF-8" ?>
<!--
 * This file is part of BibORB
 * 
 * Copyright (C) 2003-2008 Guillaume Gardey <glinmac+biborb@gmail.com>
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 * 
-->
<!--
 * File: extract_ids.xsl
 *
 * Description:
 *
 *  
 *
 *
-->

<xsl:stylesheet
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:bibtex="http://bibtexml.sf.net/"
    version="1.0">
  
    <xsl:output method="text" encoding="UTF-8"/>
	
	<!-- include generic parameters -->
	<xsl:include href="xsl/parameters.xsl"/>

	<xsl:template match="/">
        <xsl:choose>
            <!-- Sort by Author -->
            <xsl:when test="$sort = 'author'">
                <xsl:for-each select="XPATH_QUERY">
                    <xsl:sort select=".//bibtex:lastName" order="{$sort_order}" data-type="text"/>
                    <xsl:value-of select='@id'/><xsl:if test='position()!=last()'>|</xsl:if>
                </xsl:for-each>
            </xsl:when>
            <!-- Sort by year -->
            <xsl:when test="$sort = 'year'">
                <xsl:for-each select="XPATH_QUERY">
                    <xsl:sort select=".//bibtex:year" order="{$sort_order}" data-type="number"/>
                    <xsl:value-of select='@id'/><xsl:if test='position()!=last()'>|</xsl:if>
                </xsl:for-each>
            </xsl:when>
            <!-- Sort by title -->
            <xsl:when test="$sort = 'title'">
                <xsl:for-each select="XPATH_QUERY">
                    <xsl:sort select=".//bibtex:title" order="{$sort_order}" data-type="text"/>
                    <xsl:value-of select='@id'/><xsl:if test='position()!=last()'>|</xsl:if>
                </xsl:for-each>
            </xsl:when>
            <!-- Sort by ID -->
            <xsl:when test="$sort = 'ID'">
                <xsl:for-each select="XPATH_QUERY">
                    <xsl:sort select="@id" order="{$sort_order}" data-type="text"/>
                    <xsl:value-of select='@id'/><xsl:if test='position()!=last()'>|</xsl:if>
                </xsl:for-each>
            </xsl:when>
            <!-- Sort by date added -->
            <xsl:when test="$sort = 'dateAdded'">
                <xsl:for-each select="XPATH_QUERY">
                    <xsl:sort select="number(translate(.//bibtex:dateAdded,'-',''))" order="{$sort_order}" data-type="number"/>
                    <xsl:value-of select='@id'/><xsl:if test='position()!=last()'>|</xsl:if>
                </xsl:for-each>
            </xsl:when>
            <!-- Sort by last date modified -->
            <xsl:when test="$sort = 'lastDateModified'">
                <xsl:for-each select="XPATH_QUERY">
                    <xsl:sort select="number(translate(.//bibtex:lastDateModified,'-',''))" order="{$sort_order}" data-type="number"/>
                    <xsl:value-of select='@id'/><xsl:if test='position()!=last()'>|</xsl:if>
                </xsl:for-each>
            </xsl:when>
            <xsl:otherwise>
                <xsl:for-each select="XPATH_QUERY">
                    <xsl:value-of select='@id'/><xsl:if test='position()!=last()'>|</xsl:if>
                </xsl:for-each>
            </xsl:otherwise>
        </xsl:choose>
	</xsl:template>

	
</xsl:stylesheet>

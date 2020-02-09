<?php
	
	
	function PageCDN_rewriter_rewrite_google_fonts( $match )
	{
		Global	$PageCDN_fonts				,
				$PageCDN_max_font_URLs		;
		
		if( count( $PageCDN_fonts ) > $PageCDN_max_font_URLs )
		{
			return '';
		}
		
		if( strlen( $match[0] ) && strlen( $match[1] ) )
		{
			$url	= trim( $match[1] , '\'"' );
			
			$url	= parse_url( $url );
			
			if( isset( $url['query'] ) )
			{
				parse_str( $url['query'] , $vars );
				
				foreach( $vars as $key => $val )
				{
					if( $key == 'family' )
					{
						$family	= $val;
					}
				}
			}
			
			foreach( explode( '|' , $family ) as $ifamily )
			{
				if( strpos( $ifamily , ':' ) )
				{
					$ifamily	= substr( $ifamily , 0 , strpos( $ifamily , ':' ) );
				}
				
				$family_name	= str_replace( ' ' , '-' , strtolower( $ifamily ) ) . '.css';
				
				$PageCDN_fonts[$family_name]	= true;
				
				if( count( $PageCDN_fonts ) > $PageCDN_max_font_URLs )
				{
					return '';
				}
			}
			
			return '';
		}
	}
	
	
	function PageCDN_rewriter_rewrite_easy_fonts( $match )
	{
		Global	$PageCDN_fonts				,
				$PageCDN_max_font_URLs		;
		
		if( count( $PageCDN_fonts ) > $PageCDN_max_font_URLs )
		{
			return '';
		}
		
		if( strlen( $match[0] ) && strlen( $match[1] ) )
		{
			$url	= trim( $match[1] , '\'"' );
			
			$url	= str_replace( 'http://pagecdn.io/lib/easyfonts/' , '' , $url );
			
			$family	= str_replace( 'https://pagecdn.io/lib/easyfonts/' , '' , $url );
			
			if( strpos( $family , '?' ) !== false )
			{
				$family	= substr( $family , 0 , strpos( $family , '?' ) );
			}
			
			if( $family == 'fonts.css' )		#	Importing all fonts
			{
				$PageCDN_fonts	= array_fill( 0 , $PageCDN_max_font_URLs + 1 , '' );
				
				return '';
			}
			
			$PageCDN_fonts[$family]		= true;
			
			return '';
		}
	}
	
	function PageCDN_rewriter_insert_fonts( $html )
	{
		Global	$PageCDN_fonts				,
				$PageCDN_max_font_URLs		;
		
		$code	= '';
		
		$base	= 'https://pagecdn.io/lib/easyfonts/';
		
		if( count( $PageCDN_fonts ) > $PageCDN_max_font_URLs )
		{
			$code	.= "\n<link rel=\"stylesheet\" href=\"{$base}fonts.css\" />";
		}
		else
		{
			foreach( $PageCDN_fonts as $family => $scrap )
			{
				$code	.= "\n<link rel=\"stylesheet\" href=\"{$base}{$family}\" />";
			}
		}
		
		$pos	= strpos( $html , '</head>' );
		
		if( $pos === false )
		{
			$pos	= strpos( strtolower( $html ) , '</head>' );
		}
		
		if( $pos === false )
		{
			$html	= $code . $html;
		}
		else
		{
			$html	= substr_replace( $html, $code.'</head>' , $pos, 7 );
		}
		
		return $html;
	}
	
	
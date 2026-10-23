        ORG   &HC000
;
; EQU's (BIOS)
;
CHGMOD: EQU   &H5F
CHGET:  EQU   &H9F
CLS:    EQU   &HC3
SUBROM: EQU   &H015F
KNJPRT: EQU   &H01BD
;
; EQU's (Systeem variabelen)
;
VDP2RG: EQU   &HF3E1
ATRBYT: EQU   &HF3F2
ACPAGE: EQU   &HFAF6
LOGOPR: EQU   &HFB02
GRPACX: EQU   &HFCB7
GRPACY: EQU   &HFCB9
VDP9RG: EQU   &HFFE8
;
; PUT KANJI demo - BiFi'99 - Interlaced version
; Non-interlace is very easy to extract from
; this source.
;
START:  LD    A,7             ; SCREEN 7
        CALL  &H5F

        LD    A,(VDP9RG)      ; Interlace on
        OR    12
        LD    (VDP9RG),A
        OUT   (&H99),A
        LD    A,128+9
        OUT   (&H99),A

        LD    A,63            ; SET PAGE 1,1
        LD    (VDP2RG),A
        OUT   (&H99),A
        LD    A,128+2
        OUT   (&H99),A
        LD    A,1
        LD    (ACPAGE),A

        XOR   A
        CALL  CLS

        LD    HL,180          ; X = 180
        LD    (GRPACX),HL
        LD    HL,100          ; Y = 100
        LD    (GRPACY),HL
        LD    HL,TJIS

PRT_LP: LD    C,(HL)          ; Read Jis-code
        INC   HL
        LD    B,(HL)
        INC   HL
        LD    A,B             ; Check for table ending
        OR    C
        JR    Z,PRTDNE

        CALL  PRINT           ; PUT KANJI twice
        JR    PRT_LP

PRTDNE: CALL  CHGET           ; Wait for keypress
        LD    A,(VDP9RG)      ; Interlace off
        AND   243
        LD    (VDP9RG),A
        OUT   (&H99),A
        LD    A,128+9
        OUT   (&H99),A
        XOR   A               ; SCREEN 0
        JP    CHGMOD
;
PRINT:  LD    DE,(GRPACX)
        PUSH  DE
        LD    E,0
        CALL  KANJI
        POP   DE
        LD    (GRPACX),DE
        LD    E,1

KANJI:  LD    A,14            ; Color
        LD    (ATRBYT),A
        LD    A,0             ; Logic operation
        LD    (LOGOPR),A
        LD    A,E             ; SET PAGE (active part)
        LD    (ACPAGE),A
        INC   A               ; Printmode

        LD    IX,KNJPRT
        JP    SUBROM
;
TJIS:   DW    &H234D,&H2353,&H2358,&H2342
        DW    &H2341,&H2353,&H2349,&H2343,0

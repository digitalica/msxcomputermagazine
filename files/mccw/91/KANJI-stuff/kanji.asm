;
; KANJI-driver check - BiFi'96/'99
;
; Out: Z-flag set when no driver
;
KNJCHK: LD    A,255
        LD    DE,&H1100       ; D = ID-code, E = Command
        CALL  &HFFCA
        INC   A
        RET
;
; Read KANJI-mode - BiFi'96/'99
;
; Out: A=Current KANJI-mode
;         =0   ANK
;         =1   KANJI0
;         =2   KANJI1
;         =3   KANJI2
;         =4   KANJI3
;
RD_KNJ: LD    DE,&H1100
        JP    &HFFCA
;
; Set KANJI-mode - BiFi'96/'99
;
; In:  A = KANJI-mode
;
WRTKNJ: LD    DE,&H1101
        JP    &HFFCA

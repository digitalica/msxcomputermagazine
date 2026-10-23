
FCB:     equ #005C

refile:  ld de,FCB
         ld c,#0F
         call #5

         ld de,file
         ld c,#1A
         call #5

         ld hl,(FCB+16)
         ld (FCB+14),HL

         ld hl,0
         ld (FCB+33),hl
         ld (FCB+35),hl

         ld hl,1
         ld de,FCB
         ld c,#27
         call #5

         ret


waitforkey:
; checks for the press of
; the SPACE button

pressed:
 in a,(#AA)
 and #F0
 or 8
 out (#AA),a
 in a,(#A9)
 and 1
 jp z,pressed
depressed:
 in a,(#AA)
 and #F0
 or 8
 out (#AA),a
 in a,(#A9)
 and 1
 jp nz,depressed

 ret

waitnotkey:
 call ispressed
 jp z,waitnotkey
 ret

ispressed:
 in a,(#AA)
 and #F0
 or 8
 out (#AA),a
 in a,(#A9)
 and 1
 ret

retrace:  db 0
rgsav     equ #F3DF
spr_pat   equ #38
spr_atr   equ #1B

wr_VDP:   out (#99),a
          ld a,b
          or #80
          out (#99),a
          ret

scrmode:  ld (#FCAF),a
          rst #30
          defb 0
          defw #5F
          ret

